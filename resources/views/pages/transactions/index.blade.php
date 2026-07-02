@extends('layouts.duit')
@section('title', 'Keuangan')

@push('styles')
<style>
  .page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;}
  .page-title{font-size:22px;font-weight:700;}
  .page-sub{font-size:13px;color:var(--text-muted);margin-top:4px;}
  .card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);padding:20px;}
  .card-label{font-size:10px;font-weight:700;letter-spacing:.12em;color:var(--text-muted);text-transform:uppercase;margin-bottom:8px;}

  /* Stats */
  .stats-row{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:20px;}
  .stat-card{border-radius:var(--radius-md);padding:18px;position:relative;overflow:hidden;}
  .stat-card.income-card{background:var(--bg-card);border-top:3px solid var(--teal);border:1px solid var(--border);border-top:3px solid var(--teal);}
  .stat-card.expense-card{background:var(--bg-card);border-top:3px solid var(--red);border:1px solid var(--border);border-top:3px solid var(--red);}
  .stat-card.balance-card{background:var(--bg-card);border-top:3px solid var(--blue);border:1px solid var(--border);border-top:3px solid var(--blue);}
  .stat-label{font-size:10px;font-weight:700;letter-spacing:.1em;color:var(--text-muted);text-transform:uppercase;}
  .stat-val{font-size:22px;font-weight:800;margin-top:6px;}
  .income-card .stat-val{color:var(--teal);}
  .expense-card .stat-val{color:var(--red);}
  .balance-card .stat-val{color:var(--blue);}

  /* Main grid */
  .main-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;}

  /* Form */
  .form-group{margin-bottom:14px;}
  .form-label{font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;display:block;}
  .form-row{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
  .form-input,.form-select{width:100%;background:var(--bg-input);border:1px solid var(--border);border-radius:var(--radius-sm);padding:10px 14px;color:var(--text-main);font-size:14px;outline:none;font-family:inherit;transition:border-color .2s;}
  .form-input:focus,.form-select:focus{border-color:var(--teal);}
  .form-select option{background:var(--bg-card);}
  .rp-wrap{position:relative;}
  .rp-prefix{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:13px;pointer-events:none;}
  .rp-wrap .form-input{padding-left:32px;font-weight:700;}
  .rp-hint{font-size:11px;color:var(--text-dim);margin-top:4px;}
  .btn-save{width:100%;background:var(--teal);color:#000;border:none;border-radius:var(--radius-sm);padding:12px;font-weight:700;font-size:14px;cursor:pointer;margin-top:4px;}
  .btn-save:hover{opacity:.9;}

  /* Chart */
  .chart-wrap{position:relative;height:160px;}

  /* Budget rings */
  .budget-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(90px,1fr));gap:14px;margin-top:14px;}
  .budget-item{display:flex;flex-direction:column;align-items:center;gap:6px;}
  .budget-ring-wrap{position:relative;width:64px;height:64px;}
  .budget-ring-wrap canvas{position:absolute;top:0;left:0;}
  .budget-ring-center{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;color:var(--text-main);}
  .budget-name{font-size:11px;color:var(--text-muted);text-align:center;}
  .budget-amount{font-size:12px;font-weight:700;color:var(--text-main);}

  /* Activity map */
  .activity-section{margin-bottom:20px;}
  .activity-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:4px;margin-top:14px;}
  .activity-day-label{font-size:10px;color:var(--text-dim);text-align:center;margin-bottom:4px;}
  .activity-week-labels{display:grid;grid-template-columns:repeat(7,1fr);gap:4px;margin-bottom:4px;}
  .activity-cell{height:28px;border-radius:4px;background:var(--bg-input);cursor:pointer;transition:transform .1s;position:relative;}
  .activity-cell:hover{transform:scale(1.1);}
  .activity-cell .tooltip{display:none;position:absolute;bottom:120%;left:50%;transform:translateX(-50%);background:#000;color:#fff;font-size:10px;padding:4px 8px;border-radius:4px;white-space:nowrap;z-index:10;}
  .activity-cell:hover .tooltip{display:block;}

  /* Recent transactions */
  .recent-section{margin-bottom:20px;}
  .tx-item{display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid var(--border);}
  .tx-item:last-child{border-bottom:none;}
  .tx-icon{width:36px;height:36px;border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;}
  .tx-icon.income{background:rgba(0,212,170,.15);}
  .tx-icon.expense{background:rgba(255,91,91,.15);}
  .tx-info{flex:1;min-width:0;}
  .tx-desc{font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
  .tx-meta{font-size:11px;color:var(--text-muted);margin-top:2px;}
  .tx-amount{font-size:14px;font-weight:800;flex-shrink:0;}
  .tx-amount.income{color:var(--teal);}
  .tx-amount.expense{color:var(--red);}
  .btn-danger-sm{background:none;border:none;color:var(--text-dim);cursor:pointer;font-size:16px;padding:4px;}
  .btn-danger-sm:hover{color:var(--red);}
  .empty-state{text-align:center;padding:40px 20px;color:var(--text-dim);}
  .alert-success{background:rgba(0,212,170,.1);border:1px solid rgba(0,212,170,.3);color:var(--teal);padding:12px 16px;border-radius:var(--radius-sm);margin-bottom:16px;font-size:13px;}
  .btn-primary{background:var(--teal);color:#000;border:none;border-radius:var(--radius-sm);padding:10px 20px;font-weight:700;font-size:13px;cursor:pointer;}

  @media (max-width: 768px) {
    .page-header{flex-direction:column;align-items:flex-start;gap:10px;}
    .stats-row{grid-template-columns:1fr;}
    .main-grid{grid-template-columns:1fr;}
    .stat-val{font-size:18px;}
    .form-row{grid-template-columns:1fr;}
    .budget-grid{grid-template-columns:repeat(3,1fr);}
  }
</style>
@endpush

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">💰 Keuangan</div>
    <div class="page-sub">Lacak setiap rupiah</div>
  </div>
</div>

@if(session('success'))
  <div class="alert-success">✅ {{ session('success') }}</div>
@endif

<!-- Stats -->
<div class="stats-row">
  <div class="stat-card income-card">
    <div class="stat-label">Pemasukan</div>
    <div class="stat-val">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
  </div>
  <div class="stat-card expense-card">
    <div class="stat-label">Pengeluaran</div>
    <div class="stat-val">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
  </div>
  <div class="stat-card balance-card">
    <div class="stat-label">Saldo</div>
    <div class="stat-val">Rp {{ number_format($totalBalance, 0, ',', '.') }}</div>
  </div>
</div>

<!-- Main Grid: Form + Chart -->
<div class="main-grid">
  <!-- Form Tambah -->
  <div class="card">
    <div class="card-label">Tambah Transaksi</div>
    <form method="POST" action="{{ route('transactions.store') }}" id="tx-form">
      @csrf
      <div class="form-group">
        <div class="form-row">
          <div>
            <label class="form-label">Tipe</label>
            <select class="form-select" name="type" id="type-select">
              <option value="expense">Pengeluaran</option>
              <option value="income">Pemasukan</option>
            </select>
          </div>
          <div>
            <label class="form-label">Kategori</label>
            <select class="form-select" name="category">
              <option value="">-- Pilih --</option>
              <option>🍔 Makan</option>
              <option>🚗 Transport</option>
              <option>🛍️ Belanja</option>
              <option>💊 Kesehatan</option>
              <option>🎮 Hiburan</option>
              <option>📱 Tagihan</option>
              <option>💼 Gaji</option>
              <option>💻 Freelance</option>
              <option>📈 Bisnis</option>
              <option>💰 Lainnya</option>
            </select>
          </div>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Jumlah (Rp)</label>
        <div class="rp-wrap">
          <span class="rp-prefix">Rp</span>
          <input class="form-input" type="text" inputmode="numeric" id="amount-display" placeholder="0" required>
        </div>
        <input type="hidden" name="amount" id="amount-raw">
        <div class="rp-hint">Contoh: 1.250.000 (titik = ribuan)</div>
      </div>
      <div class="form-group">
        <label class="form-label">Keterangan</label>
        <input class="form-input" type="text" name="description" placeholder="Misal: makan siang di warteg">
      </div>
      <div class="form-group">
        <label class="form-label">Tanggal</label>
        <input class="form-input" type="date" name="date" value="{{ date('Y-m-d') }}" required>
      </div>
      <button class="btn-save" type="submit">Simpan Transaksi</button>
    </form>
  </div>

  <!-- Chart 7 Hari -->
  <div class="card">
    <div class="card-label">7 Hari Terakhir</div>
    <div class="chart-wrap" style="margin-top:14px">
      <canvas id="weekChart"></canvas>
    </div>
    <div style="display:flex;gap:16px;margin-top:10px;font-size:12px;">
      <span style="display:flex;align-items:center;gap:6px"><span style="width:10px;height:10px;border-radius:50%;background:var(--teal);display:inline-block"></span>Masuk</span>
      <span style="display:flex;align-items:center;gap:6px"><span style="width:10px;height:10px;border-radius:50%;background:var(--red);display:inline-block"></span>Keluar</span>
    </div>
  </div>
</div>

<!-- Budget per Kategori -->
@if($budgetByCategory->count() > 0)
<div class="card" style="margin-bottom:20px">
  <div class="card-label">Budget per Kategori</div>
  <div class="budget-grid">
    @php $totalExp = $budgetByCategory->sum('total'); @endphp
    @foreach($budgetByCategory as $i => $b)
    @php
      $pct = $totalExp > 0 ? round(($b->total / $totalExp) * 100) : 0;
      $colors = ['#00d4aa','#f5a623','#4a9eff','#ff5b5b','#a78bfa','#4cde80','#f97316'];
      $color = $colors[$i % count($colors)];
      $amount = $b->total >= 1000000 ? 'Rp '.number_format($b->total/1000000, 1, ',', '.').'jt' : 'Rp '.number_format($b->total, 0, ',', '.');
    @endphp
    <div class="budget-item">
      <div class="budget-ring-wrap">
        <canvas id="ring-{{ $i }}" width="64" height="64" data-pct="{{ $pct }}" data-color="{{ $color }}"></canvas>
        <div class="budget-ring-center">{{ $pct }}%</div>
      </div>
      <div class="budget-name">{{ $b->category }}</div>
      <div class="budget-amount" style="color:{{ $color }}">{{ $amount }}</div>
    </div>
    @endforeach
  </div>
</div>
@endif

<!-- Activity Map -->
<div class="card activity-section">
  <div class="card-label">Peta Aktivitas Keuangan Bulan Ini</div>
  <div class="activity-week-labels">
    @foreach(['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $d)
      <div class="activity-day-label">{{ $d }}</div>
    @endforeach
  </div>
  <div class="activity-grid" id="activity-grid">
    @php
      $startOfMonth = \Carbon\Carbon::now()->startOfMonth();
      $endOfMonth   = \Carbon\Carbon::now()->endOfMonth();
      $firstDow     = $startOfMonth->dayOfWeek;
      $maxExpense   = $activityMap->max('expense') ?: 1;
    @endphp
    @for($i = 0; $i < $firstDow; $i++)
      <div></div>
    @endfor
    @for($day = 1; $day <= $endOfMonth->day; $day++)
      @php
        $date = \Carbon\Carbon::now()->startOfMonth()->addDays($day - 1)->format('Y-m-d');
        $data = $activityMap->get($date);
        $exp  = $data ? $data->expense : 0;
        $inc  = $data ? $data->income  : 0;
        $intensity = $exp > 0 ? max(0.15, min(1, $exp / $maxExpense)) : 0;
        $today = \Carbon\Carbon::today()->format('Y-m-d');
        $isToday = $date === $today;
        $bg = $exp > 0
          ? 'rgba(255,91,91,' . $intensity . ')'
          : ($inc > 0 ? 'rgba(0,212,170,0.3)' : 'var(--bg-input)');
        $border = $isToday ? 'border:2px solid var(--teal);' : '';
      @endphp
      <div class="activity-cell" style="background:{{ $bg }};{{ $border }}">
        <div class="tooltip">
          {{ $day }}: {{ $exp > 0 ? '-Rp '.number_format($exp, 0, ',', '.') : ($inc > 0 ? '+Rp '.number_format($inc, 0, ',', '.') : 'Tidak ada') }}
        </div>
      </div>
    @endfor
  </div>
  <div style="display:flex;gap:16px;margin-top:10px;font-size:11px;color:var(--text-muted)">
    <span>🟩 Pemasukan</span>
    <span>🟥 Pengeluaran (lebih gelap = lebih besar)</span>
    <span>⬜ Tidak ada transaksi</span>
  </div>
</div>

<!-- Recent Transactions -->
<div class="card recent-section">
  <div class="card-label" style="margin-bottom:14px">Transaksi Terbaru</div>
  @if($transactions->isEmpty())
    <div class="empty-state">💸 Belum ada transaksi</div>
  @else
    @foreach($transactions as $t)
    <div class="tx-item">
      <div class="tx-icon {{ $t->type }}">{{ $t->type == 'income' ? '⬇️' : '⬆️' }}</div>
      <div class="tx-info">
        <div class="tx-desc">{{ $t->description ?? ($t->category ?? 'Transaksi') }}</div>
        <div class="tx-meta">{{ \Carbon\Carbon::parse($t->date)->format('d M Y') }} · {{ $t->category ?? '-' }}</div>
      </div>
      <div class="tx-amount {{ $t->type }}">
        {{ $t->type == 'income' ? '+' : '-' }}Rp {{ number_format($t->amount, 0, ',', '.') }}
      </div>
      <form method="POST" action="{{ route('transactions.destroy', $t) }}" onsubmit="return confirm('Hapus?')">
        @csrf @method('DELETE')
        <button class="btn-danger-sm" type="submit" title="Hapus">🗑️</button>
      </form>
    </div>
    @endforeach
    @if($transactions->hasPages())
<div style="display:flex;justify-content:space-between;align-items:center;margin-top:14px;font-size:13px;">
  @if($transactions->onFirstPage())
    <span style="color:var(--text-dim)">← Sebelumnya</span>
  @else
    <a href="{{ $transactions->previousPageUrl() }}" style="color:var(--teal);text-decoration:none;font-weight:600;">← Sebelumnya</a>
  @endif
  <span style="color:var(--text-muted)">Halaman {{ $transactions->currentPage() }} dari {{ $transactions->lastPage() }}</span>
  @if($transactions->hasMorePages())
    <a href="{{ $transactions->nextPageUrl() }}" style="color:var(--teal);text-decoration:none;font-weight:600;">Selanjutnya →</a>
  @else
    <span style="color:var(--text-dim)">Selanjutnya →</span>
  @endif
</div>
@endif

@endsection

@push('scripts')
<script>
// Format Rupiah
const amountDisplay = document.getElementById('amount-display');
const amountRaw = document.getElementById('amount-raw');
amountDisplay.addEventListener('input', function(e) {
  let value = e.target.value.replace(/\D/g, '');
  amountRaw.value = value;
  e.target.value = value ? new Intl.NumberFormat('id-ID').format(value) : '';
});
document.getElementById('tx-form').addEventListener('submit', function(e) {
  if (!amountRaw.value) { e.preventDefault(); alert('Jumlah harus diisi'); }
});

// Bar Chart 7 hari
const ctx = document.getElementById('weekChart').getContext('2d');
const weekData = @json($last7Days);
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: weekData.map(d => d.label),
    datasets: [
      { label: 'Masuk', data: weekData.map(d => d.income), backgroundColor: 'rgba(0,212,170,0.8)', borderRadius: 6 },
      { label: 'Keluar', data: weekData.map(d => d.expense), backgroundColor: 'rgba(255,91,91,0.8)', borderRadius: 6 }
    ]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false }, tooltip: {
      callbacks: { label: ctx => 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw) }
    }},
    scales: {
      x: { grid: { color: '#2a2f45' }, ticks: { color: '#7880a0' } },
      y: { grid: { color: '#2a2f45' }, ticks: { color: '#7880a0', callback: v => v >= 1000000 ? (v/1000000)+'jt' : v >= 1000 ? (v/1000)+'rb' : v } }
    }
  }
});

// Budget rings
document.querySelectorAll('[id^="ring-"]').forEach(canvas => {
  const pct = parseFloat(canvas.dataset.pct);
  const color = canvas.dataset.color;
  new Chart(canvas.getContext('2d'), {
    type: 'doughnut',
    data: {
      datasets: [{ data: [pct, 100 - pct], backgroundColor: [color, '#232840'], borderWidth: 0 }]
    },
    options: { cutout: '72%', plugins: { legend: { display: false }, tooltip: { enabled: false } }, animation: { duration: 800 } }
  });
});
</script>
@endpush
