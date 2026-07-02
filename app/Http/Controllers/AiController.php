<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class AiController extends Controller
{
    // ============================================================
    // PILIH PROVIDER: 'groq' atau 'deepseek'
    // Ganti nilai di bawah sesuai API key yang kamu punya
    // ============================================================
    private string $provider = 'groq';

    private array $config = [
        'groq' => [
            'url'   => 'https://api.groq.com/openai/v1/chat/completions',
            'model' => 'llama-3.3-70b-versatile',   // gratis, pintar, cepat
            'env'   => 'GROQ_API_KEY',
        ],
        'deepseek' => [
            'url'   => 'https://api.deepseek.com/v1/chat/completions',
            'model' => 'deepseek-chat',              // murah (~$0.07/1M token)
            'env'   => 'DEEPSEEK_API_KEY',
        ],
    ];

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'nullable|array|max:20',
        ]);

        $user    = auth()->user();
        $context = $this->buildFinancialContext($user);
        $cfg     = $this->config[$this->provider];
        $apiKey  = config('services.' . $this->provider . '.key');

        if (!$apiKey) {
            return response()->json([
                'error' => 'API key belum diset. Tambahkan ' . $cfg['env'] . ' di .env dan Railway.'
            ], 500);
        }

        // Bangun pesan: system + history + pesan baru
        $messages = [
            ['role' => 'system', 'content' => $this->buildSystemPrompt($context)],
        ];

        foreach ($request->input('history', []) as $h) {
            if (in_array($h['role'] ?? '', ['user', 'assistant']) && isset($h['content'])) {
                $messages[] = ['role' => $h['role'], 'content' => $h['content']];
            }
        }

        $messages[] = ['role' => 'user', 'content' => $request->message];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type'  => 'application/json',
        ])->timeout(30)->post($cfg['url'], [
            'model'       => $cfg['model'],
            'max_tokens'  => 1024,
            'temperature' => 0.7,
            'messages'    => $messages,
        ]);

        if ($response->failed()) {
            $status = $response->status();
            $msg = match($status) {
                401     => 'API Key tidak valid. Cek ' . $cfg['env'] . ' di .env',
                429     => 'Batas request tercapai. Coba lagi sebentar.',
                default => 'Gagal menghubungi AI (' . $status . '). Coba lagi.'
            };
            return response()->json(['error' => $msg], 500);
        }

        $data  = $response->json();
        $reply = $data['choices'][0]['message']['content'] ?? 'Maaf, tidak ada respons dari AI.';

        return response()->json(['reply' => $reply]);
    }

    // ============================================================
    // Ambil data keuangan real dari database user
    // ============================================================
    private function buildFinancialContext($user): array
    {
        $now = Carbon::now();

        $totalIn  = Transaction::where('user_id', $user->id)->where('type', 'masuk')->sum('amount');
        $totalOut = Transaction::where('user_id', $user->id)->where('type', 'keluar')->sum('amount');
        $balance  = $totalIn - $totalOut;

        $monthIn  = Transaction::where('user_id', $user->id)->where('type', 'masuk')
            ->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->sum('amount');
        $monthOut = Transaction::where('user_id', $user->id)->where('type', 'keluar')
            ->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->sum('amount');

        $lastMonth    = $now->copy()->subMonth();
        $lastMonthIn  = Transaction::where('user_id', $user->id)->where('type', 'masuk')
            ->whereMonth('created_at', $lastMonth->month)->whereYear('created_at', $lastMonth->year)->sum('amount');
        $lastMonthOut = Transaction::where('user_id', $user->id)->where('type', 'keluar')
            ->whereMonth('created_at', $lastMonth->month)->whereYear('created_at', $lastMonth->year)->sum('amount');

        $recent = Transaction::where('user_id', $user->id)
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($t) => [
                'tipe'       => $t->type === 'masuk' ? 'Pemasukan' : 'Pengeluaran',
                'jumlah'     => 'Rp ' . number_format($t->amount, 0, ',', '.'),
                'kategori'   => $t->category->name ?? '-',
                'keterangan' => $t->description ?? '-',
                'tanggal'    => Carbon::parse($t->created_at)->translatedFormat('d M Y'),
            ]);

        $topCategories = Transaction::where('user_id', $user->id)
            ->where('type', 'keluar')
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->with('category')
            ->get()
            ->groupBy(fn($t) => $t->category->name ?? 'Lainnya')
            ->map(fn($g) => $g->sum('amount'))
            ->sortDesc()
            ->take(3)
            ->map(fn($v, $k) => "$k: Rp " . number_format($v, 0, ',', '.'));

        $goals = Goal::where('user_id', $user->id)->get()->map(fn($g) => [
            'nama'      => $g->title,
            'target'    => 'Rp ' . number_format($g->target_amount, 0, ',', '.'),
            'terkumpul' => 'Rp ' . number_format($g->current_amount, 0, ',', '.'),
            'persen'    => ($g->target_amount > 0
                ? round($g->current_amount / $g->target_amount * 100) : 0) . '%',
        ]);

        return compact(
            'balance', 'monthIn', 'monthOut',
            'lastMonthIn', 'lastMonthOut',
            'recent', 'topCategories', 'goals', 'user', 'now'
        );
    }

    // ============================================================
    // System prompt dengan data keuangan real
    // ============================================================
    private function buildSystemPrompt(array $ctx): string
    {
        $name     = $ctx['user']->name ?? 'Pengguna';
        $date     = $ctx['now']->translatedFormat('l, d F Y');
        $balance  = 'Rp ' . number_format($ctx['balance'],      0, ',', '.');
        $monthIn  = 'Rp ' . number_format($ctx['monthIn'],      0, ',', '.');
        $monthOut = 'Rp ' . number_format($ctx['monthOut'],     0, ',', '.');
        $lastIn   = 'Rp ' . number_format($ctx['lastMonthIn'],  0, ',', '.');
        $lastOut  = 'Rp ' . number_format($ctx['lastMonthOut'], 0, ',', '.');

        $recentText = $ctx['recent']->map(
            fn($t) => "- [{$t['tanggal']}] {$t['tipe']} {$t['jumlah']} ({$t['kategori']}): {$t['keterangan']}"
        )->join("\n");

        $topCatText = $ctx['topCategories']->map(fn($v) => "- $v")->join("\n") ?: '- Belum ada data';

        $goalsText = $ctx['goals']->isEmpty() ? 'Belum ada goals.' :
            $ctx['goals']->map(
                fn($g) => "- {$g['nama']}: {$g['terkumpul']} / {$g['target']} ({$g['persen']})"
            )->join("\n");

        return <<<PROMPT
Kamu adalah asisten keuangan pribadi bernama "DUIT AI" untuk {$name}.
Hari ini: {$date}

═══ DATA KEUANGAN REAL {$name} ═══

RINGKASAN:
• Saldo saat ini       : {$balance}
• Pemasukan bulan ini  : {$monthIn}  (bulan lalu: {$lastIn})
• Pengeluaran bulan ini: {$monthOut} (bulan lalu: {$lastOut})

TOP PENGELUARAN BULAN INI:
{$topCatText}

10 TRANSAKSI TERAKHIR:
{$recentText}

GOALS / TABUNGAN:
{$goalsText}

═══ INSTRUKSI ═══
- Jawab dalam Bahasa Indonesia yang santai dan ramah
- SELALU dasarkan jawaban pada data nyata di atas, jangan mengarang
- Kalau ada perbandingan bulan ini vs bulan lalu, sebutkan tren-nya
- Format angka: Rp X.XXX.XXX
- Berikan insight & saran konkret yang actionable
- Jawaban ringkas 2-3 paragraf, boleh pakai bullet point
- Boleh pakai emoji secukupnya 😊
- Kalau data tidak tersedia, katakan jujur
PROMPT;
    }
}
