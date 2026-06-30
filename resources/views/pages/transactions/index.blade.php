@extends('layouts.duit')
@section('title', 'Transaksi')

@push('styles')
<style>
  .page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;}
  .page-title{font-size:22px;font-weight:700;}
  .page-sub{font-size:13px;color:var(--text-muted);margin-top:4px;}
  .grid-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px;}
  .card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);padding:20px;}
  .card-label{font-size:10px;font-weight:700;letter-spacing:.12em;color:var(--text-muted);text-transform:uppercase;margin-bottom:8px;}
  .stat-value{font-size:26px;font-weight:800;margin-top:4px;}
  .teal{color:var(--teal)}.amber{color:var(--amber)}.red{color:var(--red)}.blue{color:var(--blue)}
  .card.teal-top{border-top:3px solid var(--teal);}
  .card.amber-top{border-top:3px solid var(--amber);}
  .card.blue-top{border-top:3px solid var(--blue);}

  /* Form Modal */
  .btn-primary{background:var(--teal);color:#000;border:none;border-radius:var(--radius-sm);padding:10px 20px;font-weight:700;font-size:13px;cursor:pointer;display:flex;align-items:center;gap:6px;}
  .btn-primary:hover{opacity:.9;}
  .btn-danger{background:rgba(255,91,91,.15);color:var(--red);border:1px solid rgba(255,91,91,.3);border-radius:var(--radius-sm);padding:6px 12px;font-size:12px;cursor:pointer;}
  .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:300;align-items:center;justify-content:center;}
  .modal-overlay.open{display:flex;}
  .modal-box{width:100%;max-width:480px;background:var(--bg-card);border-radius:var(--radius-lg);border:1px solid var(--border);padding:28px;animation:fadeIn .2s ease;}
  @keyframes fadeIn{from{opacity:0;transform:scale(.95)}to{opacity:1;transform:scale(1)}}
  .modal-title{font-size:18px;font-weight:700;margin-bottom:20px;display:flex;justify-content:space-between;align-items:center;}
  .modal-close{background:var(--bg-input);border:none;color:var(--text-muted);width:30px;height:30px;border-radius:50%;font-size:16px;cursor:pointer;}
  .form-group{margin-bottom:16px;}
  .form-label{font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;display:block;}
  .form-input,.form-select{width:100%;background:var(--bg-input);border:1px solid var(--border);border-radius:var(--radius-sm);padding:10px 14px;color:var(--text-main);font-size:14px;outline:none;font-family:inherit;transition:border-color .2s;}
  .form-input:focus,.form-select:focus{border-color:var(--teal);}
  .form-select option{background:var(--bg-card);}
  .type-toggle{display:flex;gap:8px;margin-bottom:16px;}
  .type-btn{flex:1;padding:10px;border-radius:var(--radius-sm);border:2px solid var(--border);background:var(--bg-input);color:var(--text-muted);font-weight:600;cursor:pointer;font-size:13px;transition:all .2s;}
  .type-btn.income.active{border-color:var(--teal);background:rgba(0,212,170,.1);color:var(--teal);}
  .type-btn.expense.active{border-color:var(--red);background:rgba(255,91,91,.1);color:var(--red);}

  /* Table */
  .table-wrap{overflow-x:auto;}
  table{width:100%;border-collapse:collapse;}
  thead th{font-size:11px;font-weight:700;letter-spacing:.1em;color:var(--text-muted);text-transform:uppercase;padding:10px 14px;text-align:left;border-bottom:1px solid var(--border);}
  tbody tr{border-bottom:1px solid var(--border);transition:background .15s;}
  tbody tr:hover{background:var(--bg-card-2,#1e2333);}
  tbody td{padding:12px 14px;font-size:13px;}
  .badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:99px;font-size:11px;font-weight:700;}
  .badge.income{background:rgba(0,212,170,.15);color:var(--teal);}
  .badge.expense{background:rgba(255,91,91,.15);color:var(--red);}
  .empty-state{text-align:center;padding:60px 20px;color:var(--text-dim);}
  .empty-state .icon{font-size:48px;margin-bottom:12px;}
  .alert-success{background:rgba(0,212,170,.1);border:1px solid rgba(0,212,170,.3);color:var(--teal);padding:12px 16px;border-radius:var(--radius-sm);margin-bottom:16px;font-size:13px;}
  .pagination-wrap{margin-top:16px;display:flex;justify-content:center;gap:6px;}
</style>
@endpush

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">💰 Transaksi</div>
    <div class="page-sub">Catat pemasukan dan pengeluaran Anda</div>
  </div>
  <button class="btn-primary" onclick="document.getElementById('modal-add').classList.add('open')">
    + Tambah Transaksi
  </button>
</div>

@if(session('success'))
  <div class="alert-success">✅ {{ session('success') }}</div>
@endif

<!-- Stats -->
<div class="grid-stats">
  <div class="card teal-top">
    <div class="card-label">Total Saldo</div>
    <div class="stat-value teal">Rp {{ number_format($totalBalance, 0, ',', '.') }}</div>
  </div>
  <div class="card teal-top">
    <div class="card-label">Pemasukan Bulan Ini</div>
    <div class="stat-value teal">Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</div>
  </div>
  <div class="card amber-top">
    <div class="card-label">Pengeluaran Bulan Ini</div>
    <div class="stat-value amber">Rp {{ number_format($monthlyExpense, 0, ',', '.') }}</div>
  </div>
</div>

<!-- Table -->
<div class="card">
  <div class="table-wrap">
    @if($transactions->isEmpty())
      <div class="empty-state">
        <div class="icon">💸</div>
        <div>Belum ada transaksi</div>
        <div style="font-size:12px;margin-top:6px">Klik "Tambah Transaksi" untuk mulai mencatat</div>
      </div>
    @else
      <table>
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Keterangan</th>
            <th>Kategori</th>
            <th>Tipe</th>
            <th>Jumlah</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach($transactions as $t)
          <tr>
            <td style="color:var(--text-muted)">{{ \Carbon\Carbon::parse($t->date)->format('d M Y') }}</td>
            <td>{{ $t->description ?? '-' }}</td>
            <td style="color:var(--text-muted)">{{ $t->category ?? '-' }}</td>
            <td><span class="badge {{ $t->type }}">{{ $t->type == 'income' ? '⬇️ Masuk' : '⬆️ Keluar' }}</span></td>
            <td style="font-weight:700" class="{{ $t->type == 'income' ? 'teal' : 'red' }}">
              {{ $t->type == 'income' ? '+' : '-' }} Rp {{ number_format($t->amount, 0, ',', '.') }}
            </td>
            <td>
              <form method="POST" action="{{ route('transactions.destroy', $t) }}" onsubmit="return confirm('Hapus transaksi ini?')">
                @csrf @method('DELETE')
                <button class="btn-danger" type="submit">Hapus</button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      <div class="pagination-wrap">{{ $transactions->links() }}</div>
    @endif
  </div>
</div>

<!-- Modal Tambah -->
<div class="modal-overlay" id="modal-add" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="modal-box">
    <div class="modal-title">
      <span>Tambah Transaksi</span>
      <button class="modal-close" onclick="document.getElementById('modal-add').classList.remove('open')">✕</button>
    </div>
    <form method="POST" action="{{ route('transactions.store') }}">
      @csrf
      <div class="type-toggle">
        <button type="button" class="type-btn income active" id="btn-income" onclick="setType('income')">⬇️ Pemasukan</button>
        <button type="button" class="type-btn expense" id="btn-expense" onclick="setType('expense')">⬆️ Pengeluaran</button>
      </div>
      <input type="hidden" name="type" id="type-input" value="income">

      <div class="form-group">
        <label class="form-label">Jumlah (Rp)</label>
        <input class="form-input" type="number" name="amount" placeholder="0" min="1" required>
      </div>
      <div class="form-group">
        <label class="form-label">Kategori</label>
        <select class="form-select" name="category">
          <option value="">-- Pilih Kategori --</option>
          <option>Gaji</option><option>Freelance</option><option>Bisnis</option>
          <option>Makanan</option><option>Transport</option><option>Belanja</option>
          <option>Kesehatan</option><option>Hiburan</option><option>Tagihan</option>
          <option>Lainnya</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Keterangan</label>
        <input class="form-input" type="text" name="description" placeholder="Misal: Gaji bulan Juni">
      </div>
      <div class="form-group">
        <label class="form-label">Tanggal</label>
        <input class="form-input" type="date" name="date" value="{{ date('Y-m-d') }}" required>
      </div>
      <button class="btn-primary" type="submit" style="width:100%;justify-content:center;padding:12px;">Simpan Transaksi</button>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
function setType(type) {
  document.getElementById('type-input').value = type;
  document.getElementById('btn-income').classList.toggle('active', type === 'income');
  document.getElementById('btn-expense').classList.toggle('active', type === 'expense');
}
</script>
@endpush
