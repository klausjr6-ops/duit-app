@extends('layouts.duit')
@section('title', 'Laporan')

@push('styles')
<style>
  .page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;}
  .page-title{font-size:22px;font-weight:700;}
  .page-sub{font-size:13px;color:var(--text-muted);margin-top:4px;}
  .card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);padding:20px;}

  /* Month selector */
  .filter-bar{display:flex;align-items:center;gap:12px;margin-bottom:24px;flex-wrap:wrap;}
  .month-btn{background:var(--bg-card);border:1px solid var(--border);color:var(--text-main);border-radius:var(--radius-sm);padding:8px 16px;cursor:pointer;font-size:13px;font-weight:600;text-decoration:none;}
  .month-btn:hover{border-color:var(--teal);}
  .month-label{font-size:16px;font-weight:700;min-width:160px;text-align:center;}
  .btn-print{background:var(--teal);color:#000;border:none;border-radius:var(--radius-sm);padding:10px 20px;font-weight:700;font-size:13px;cursor:pointer;display:flex;align-items:center;gap:8px;margin-left:auto;}
  .btn-print:hover{opacity:.9;}

  /* Summary cards */
  .summary-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px;}
  .summary-card{border-radius:var(--radius-md);padding:18px;border:1px solid var(--border);background:var(--bg-card);}
  .summary-card.income{border-top:3px solid var(--teal);}
  .summary-card.expense{border-top:3px solid var(--red);}
  .summary-card.balance{border-top:3px solid var(--blue);}
  .s-label{font-size:10px;font-weight:700;letter-spacing:.1em;color:var(--text-muted);text-transform:uppercase;}
  .s-val{font-size:22px;font-weight:800;margin-top:6px;}
  .s-val.teal{color:var(--teal);} .s-val.red{color:var(--red);} .s-val.blue{color:var(--blue);}

  /* Table */
  .table-wrap{overflow-x:auto;}
  table{width:100%;border-collapse:collapse;font-size:13px;}
  thead th{font-size:11px;font-weight:700;letter-spacing:.08em;color:var(--text-muted);text-transform:uppercase;padding:10px 12px;text-align:left;border-bottom:2px solid var(--border);white-space:nowrap;}
  tbody tr{border-bottom:1px solid var(--border);transition:background .1s;}
  tbody tr:hover{background:var(--bg-card-2,#1e2333);}
  tbody td{padding:10px 12px;vertical-align:middle;}
  .badge{display:inline-flex;align-items:center;padding:2px 8px;border-radius:99px;font-size:11px;font-weight:700;}
  .badge.income{background:rgba(0,212,170,.15);color:var(--teal);}
  .badge.expense{background:rgba(255,91,91,.15);color:var(--red);}
  .amount-income{color:var(--teal);font-weight:700;}
  .amount-expense{color:var(--red);font-weight:700;}
  .amount-balance{font-weight:700;}
  .empty-state{text-align:center;padding:60px;color:var(--text-dim);}

  /* ===== PRINT STYLES ===== */
  @media print {
    * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }

    body { background: #fff !important; color: #000 !important; margin: 0; font-family: Arial, sans-serif; }

    .sidebar, .btn-print, .filter-bar .month-btn, .no-print { display: none !important; }

    .main { margin-left: 0 !important; padding: 0 !important; }

    .print-header { display: block !important; }

    .card { background: #fff !important; border: 1px solid #ddd !important; box-shadow: none !important; border-radius: 4px !important; }

    .summary-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 12px; margin-bottom: 20px; }
    .summary-card { border: 1px solid #ddd !important; padding: 12px !important; }
    .s-label { color: #666 !important; font-size: 10px !important; }
    .s-val { font-size: 18px !important; }
    .s-val.teal { color: #00a888 !important; }
    .s-val.red { color: #cc3333 !important; }
    .s-val.blue { color: #2266cc !important; }

    table { font-size: 11px !important; }
    thead th { color: #333 !important; border-bottom: 2px solid #333 !important; background: #f5f5f5 !important; }
    tbody tr { border-bottom: 1px solid #eee !important; }
    tbody tr:hover { background: transparent !important; }
    .badge.income { background: #e6f9f5 !important; color: #00a888 !important; }
    .badge.expense { background: #ffeaea !important; color: #cc3333 !important; }
    .amount-income { color: #00a888 !important; }
    .amount-expense { color: #cc3333 !important; }

    .filter-bar { display: none !important; }
    .page-header { margin-bottom: 12px !important; }
    .page-title { color: #000 !important; font-size: 20px !important; }
    .page-sub { color: #666 !important; }

    @page { margin: 1.5cm; size: A4; }
  }

  /* Print header (hidden on screen) */
  .print-header { display: none; }

  @media (max-width: 768px) {
    .summary-grid { grid-template-columns: 1fr; }
    .filter-bar { flex-direction: column; align-items: flex-start; }
    .btn-print { margin-left: 0; width: 100%; justify-content: center; }
  }
</style>
@endpush

@section('content')

<!-- Print Header (only visible when printing) -->
<div class="print-header" style="margin-bottom:20px;border-bottom:2px solid #333;padding-bottom:12px;">
  <div style="display:flex;justify-content:space-between;align-items:flex-start;">
    <div>
      <div style="font-size:24px;font-weight:800;color:#000;">💰 DUIT</div>
      <div style="font-size:13px;color:#666;">Laporan Keuangan Pribadi</div>
    </div>
    <div style="text-align:right;font-size:12px;color:#666;">
      <div style="font-size:16px;font-weight:700;color:#000;">{{ $monthNames[$month] }} {{ $year }}</div>
      <div>Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
      <div>{{ auth()->user()->name }}</div>
    </div>
  </div>
</div>

<div class="page-header">
  <div>
    <div class="page-title">📋 Laporan Keuangan</div>
    <div class="page-sub">Rekap transaksi bulanan</div>
  </div>
</div>

<!-- Filter Bar -->
<div class="filter-bar no-print">
  @php
    $prevMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->subMonth();
    $nextMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->addMonth();
  @endphp
  <a href="?month={{ $prevMonth->month }}&year={{ $prevMonth->year }}" class="month-btn">← Prev</a>
  <div class="month-label">{{ $monthNames[$month] }} {{ $year }}</div>
  <a href="?month={{ $nextMonth->month }}&year={{ $nextMonth->year }}" class="month-btn">Next →</a>
  <button class="btn-print" onclick="window.print()">🖨️ Export PDF</button>
</div>

<!-- Summary -->
<div class="summary-grid">
  <div class="summary-card income">
    <div class="s-label">Total Pemasukan</div>
    <div class="s-val teal">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
  </div>
  <div class="summary-card expense">
    <div class="s-label">Total Pengeluaran</div>
    <div class="s-val red">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
  </div>
  <div class="summary-card balance">
    <div class="s-label">Saldo Bersih</div>
    <div class="s-val {{ $balance >= 0 ? 'blue' : 'red' }}">
      {{ $balance >= 0 ? '+' : '' }}Rp {{ number_format($balance, 0, ',', '.') }}
    </div>
  </div>
</div>

<!-- Transaction Table -->
<div class="card">
  <div style="font-size:14px;font-weight:700;margin-bottom:14px;">
    Rincian Transaksi — {{ $monthNames[$month] }} {{ $year }}
    <span style="font-size:12px;color:var(--text-muted);font-weight:400;margin-left:8px;">{{ $rows->count() }} transaksi</span>
  </div>

  @if($rows->isEmpty())
    <div class="empty-state">
      📋 Tidak ada transaksi di bulan ini
    </div>
  @else
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Tanggal</th>
          <th>Keterangan</th>
          <th>Kategori</th>
          <th style="text-align:right">Pemasukan</th>
          <th style="text-align:right">Pengeluaran</th>
          <th style="text-align:right">Saldo</th>
        </tr>
      </thead>
      <tbody>
        @foreach($rows as $i => $row)
        <tr>
          <td style="color:var(--text-muted)">{{ $i + 1 }}</td>
          <td style="white-space:nowrap">{{ $row['date'] }}</td>
          <td>{{ $row['description'] }}</td>
          <td><span class="badge {{ $row['type'] }}">{{ $row['category'] }}</span></td>
          <td style="text-align:right" class="amount-income">
            {{ $row['income'] ? 'Rp '.number_format($row['income'], 0, ',', '.') : '-' }}
          </td>
          <td style="text-align:right" class="amount-expense">
            {{ $row['expense'] ? 'Rp '.number_format($row['expense'], 0, ',', '.') : '-' }}
          </td>
          <td style="text-align:right" class="amount-balance" style="color:{{ $row['balance'] >= 0 ? 'var(--teal)' : 'var(--red)' }}">
            Rp {{ number_format($row['balance'], 0, ',', '.') }}
          </td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr style="border-top:2px solid var(--border);font-weight:700;">
          <td colspan="4" style="padding:10px 12px;font-size:13px;">TOTAL</td>
          <td style="text-align:right;padding:10px 12px;" class="amount-income">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
          <td style="text-align:right;padding:10px 12px;" class="amount-expense">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
          <td style="text-align:right;padding:10px 12px;" class="amount-balance">Rp {{ number_format($balance, 0, ',', '.') }}</td>
        </tr>
      </tfoot>
    </table>
  </div>
  @endif
</div>

<!-- Print Footer -->
<div class="print-header" style="margin-top:20px;border-top:1px solid #ddd;padding-top:10px;font-size:10px;color:#999;display:flex;justify-content:space-between;">
  <span>DUIT — Aplikasi Keuangan Pribadi</span>
  <span>{{ auth()->user()->name }} · {{ auth()->user()->email }}</span>
  <span>Halaman 1</span>
</div>

@endsection

@push('scripts')
<script>
// Auto-trigger print if url has ?print=1
if (window.location.search.includes('print=1')) {
  window.onload = () => window.print();
}
</script>
@endpush
