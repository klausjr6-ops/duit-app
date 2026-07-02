@extends('layouts.duit')
@section('title', 'Keuangan')

@push('styles')
<style>
  .page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;}
  .page-title{font-size:22px;font-weight:700;}
  .page-sub{font-size:13px;color:var(--text-muted);margin-top:4px;}
  .card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);padding:20px;}
  .card-label{font-size:10px;font-weight:700;letter-spacing:.12em;color:var(--text-muted);text-transform:uppercase;margin-bottom:8px;}
  .stats-row{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:20px;}
  .stat-card{border-radius:var(--radius-md);padding:18px;border:1px solid var(--border);background:var(--bg-card);}
  .stat-card.income-card{border-top:3px solid var(--teal);}
  .stat-card.expense-card{border-top:3px solid var(--red);}
  .stat-card.balance-card{border-top:3px solid var(--blue);}
  .stat-label{font-size:10px;font-weight:700;letter-spacing:.1em;color:var(--text-muted);text-transform:uppercase;}
  .stat-val{font-size:22px;font-weight:800;margin-top:6px;}
  .income-card .stat-val{color:var(--teal);}
  .expense-card .stat-val{color:var(--red);}
  .balance-card .stat-val{color:var(--blue);}
  .main-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;}
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
  .chart-wrap{position:relative;height:160px;}
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
  .pagination-nav{display:flex;justify-content:space-between;align-items:center;margin-top:14px;font-size:13px;}
  .pagination-nav a{color:var(--teal);text-decoration:none;font-weight:600;}
  .pagination-nav span{color:var(--text-dim);}
  .pagination-nav .page-info{color:var(--text-muted);}
  @media (max-width:768px){
    .stats-row{grid-template-columns:1fr;}
    .main-grid{grid-template-columns:1fr;}
    .form-row{grid-template-columns:1fr;}
    .page-header{flex-direction:column;align-items:flex-start;gap:10px;}
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

<div class="main-grid">
  <div class="card">
    <div class="card-label">Tambah Transaksi</div>
    <form method="POST" action="{{ route('transactions.store') }}" id="tx-form">
      @csrf
      <div class="form-group">
        <div class="form-row">
          <div>
            <label class="form-label">Tipe</label>
            <select class="form-select" name="type">
              <option value="expense">Pengeluaran</option>
              <option value="income">Pemasukan</option>
            </select>
          </div>
          <div>
            <label class="form-label">Kategori</label>
            <select class="form-select" name="category">
              <option value="">-- Pilih --</option>
              <option>Makan</option>
              <option>Transport</option>
              <option>Belanja</option>
              <option>Kesehatan</option>
              <option>Hiburan</option>
              <option>Tagihan</option>
              <option>Gaji</option>
              <option>Freelance</option>
              <option>Bisnis</option>
              <option>Lainnya</option>
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
        <div class="rp-hint">Contoh: 1.250.000</div>
      </div>
      <div class="form-group">
        <label class="form-label">Keterangan</label>
        <input class="form-input" type="text" name="description" placeholder="Misal: makan siang">
      </div>
      <div class="form-group">
        <label class="form-label">Tanggal</label>
        <input class="form-input" type="date" name="date" value="{{ date('Y-m-d') }}" required>
      </div>
      <button class="btn-save" type="submit">Simpan Transaksi</button>
    </form>
  </div>

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

<div class="card">
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
    <div class="pagination-nav">
      @if($transactions->onFirstPage())
        <span>← Sebelumnya</span>
      @else
        <a href="{{ $transactions->previousPageUrl() }}">← Sebelumnya</a>
      @endif
      <span class="page-info">Hal {{ $transactions->currentPage() }} dari {{ $transactions->lastPage() }}</span>
      @if($transactions->hasMorePages())
        <a href="{{ $transactions->nextPageUrl() }}">Selanjutnya →</a>
      @else
        <span>Selanjutnya →</span>
      @endif
    </div>
    @endif
  @endif
</div>
@endsection

@push('scripts')
<script>
document.getElementById('amount-display').addEventListener('input', function(e) {
  let value = e.target.value.replace(/\D/g, '');
  document.getElementById('amount-raw').value = value;
  e.target.value = value ? new Intl.NumberFormat('id-ID').format(value) : '';
});
document.getElementById('tx-form').addEventListener('submit', function(e) {
  if (!document.getElementById('amount-raw').value) {
    e.preventDefault();
    alert('Jumlah harus diisi');
  }
});
const weekData = @json($last7Days);
new Chart(document.getElementById('weekChart').getContext('2d'), {
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
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { color: '#2a2f45' }, ticks: { color: '#7880a0' } },
      y: { grid: { color: '#2a2f45' }, ticks: { color: '#7880a0', callback: v => v >= 1000000 ? (v/1000000)+'jt' : v >= 1000 ? (v/1000)+'rb' : v } }
    }
  }
});
</script>
@endpush
