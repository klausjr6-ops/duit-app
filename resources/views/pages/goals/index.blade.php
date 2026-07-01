@extends('layouts.duit')
@section('title', 'Goals')

@push('styles')
<style>
  .page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;}
  .page-title{font-size:22px;font-weight:700;}
  .page-sub{font-size:13px;color:var(--text-muted);margin-top:4px;}
  .card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);padding:20px;}
  .card-label{font-size:10px;font-weight:700;letter-spacing:.12em;color:var(--text-muted);text-transform:uppercase;margin-bottom:8px;}
  .btn-primary{background:var(--teal);color:#000;border:none;border-radius:var(--radius-sm);padding:10px 20px;font-weight:700;font-size:13px;cursor:pointer;display:flex;align-items:center;gap:6px;}
  .btn-primary:hover{opacity:.9;}
  .btn-danger{background:rgba(255,91,91,.15);color:var(--red);border:1px solid rgba(255,91,91,.3);border-radius:var(--radius-sm);padding:6px 12px;font-size:12px;cursor:pointer;}
  .btn-teal{background:rgba(0,212,170,.15);color:var(--teal);border:1px solid rgba(0,212,170,.3);border-radius:var(--radius-sm);padding:6px 12px;font-size:12px;cursor:pointer;}
  .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:300;align-items:center;justify-content:center;}
  .modal-overlay.open{display:flex;}
  .modal-box{width:100%;max-width:480px;background:var(--bg-card);border-radius:var(--radius-lg);border:1px solid var(--border);padding:28px;animation:fadeIn .2s ease;}
  @keyframes fadeIn{from{opacity:0;transform:scale(.95)}to{opacity:1;transform:scale(1)}}
  .modal-title{font-size:18px;font-weight:700;margin-bottom:20px;display:flex;justify-content:space-between;align-items:center;}
  .modal-close{background:var(--bg-input);border:none;color:var(--text-muted);width:30px;height:30px;border-radius:50%;font-size:16px;cursor:pointer;}
  .form-group{margin-bottom:16px;}
  .form-label{font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;display:block;}
  .form-input{width:100%;background:var(--bg-input);border:1px solid var(--border);border-radius:var(--radius-sm);padding:10px 14px;color:var(--text-main);font-size:14px;outline:none;font-family:inherit;transition:border-color .2s;}
  .form-input:focus{border-color:var(--teal);}

  /* Rp input wrap */
  .rp-wrap{position:relative;}
  .rp-prefix{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:13px;pointer-events:none;}
  .rp-wrap .form-input{padding-left:32px;font-weight:700;}
  .rp-hint{font-size:11px;color:var(--text-dim);margin-top:4px;}

  /* Summary */
  .summary-bar{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);padding:20px;margin-bottom:24px;}
  .summary-top{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;}
  .summary-title{font-size:16px;font-weight:700;}
  .summary-pct{font-size:24px;font-weight:800;color:var(--teal);}
  .progress-track{width:100%;height:8px;background:var(--bg-input);border-radius:99px;}
  .progress-fill{height:100%;border-radius:99px;background:linear-gradient(90deg,var(--teal),var(--blue));transition:width 1s ease;}
  .summary-nums{display:flex;justify-content:space-between;margin-top:8px;font-size:12px;color:var(--text-muted);}

  /* Goals grid */
  .goals-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;}
  .goal-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);padding:20px;position:relative;overflow:hidden;}
  .goal-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--teal),var(--blue));}
  .goal-emoji{font-size:32px;margin-bottom:10px;}
  .goal-title{font-size:16px;font-weight:700;margin-bottom:4px;}
  .goal-deadline{font-size:11px;color:var(--text-muted);margin-bottom:14px;}
  .goal-amounts{display:flex;justify-content:space-between;margin-bottom:8px;}
  .goal-saved{font-size:18px;font-weight:800;color:var(--teal);}
  .goal-target{font-size:13px;color:var(--text-muted);margin-top:2px;}
  .goal-progress-track{width:100%;height:6px;background:var(--bg-input);border-radius:99px;margin-bottom:14px;}
  .goal-progress-fill{height:100%;border-radius:99px;background:linear-gradient(90deg,var(--teal),var(--blue));}
  .goal-pct{font-size:12px;color:var(--text-muted);margin-bottom:14px;}
  .goal-actions{display:flex;gap:8px;}
  .empty-state{text-align:center;padding:60px 20px;color:var(--text-dim);}
  .empty-state .icon{font-size:48px;margin-bottom:12px;}
  .alert-success{background:rgba(0,212,170,.1);border:1px solid rgba(0,212,170,.3);color:var(--teal);padding:12px 16px;border-radius:var(--radius-sm);margin-bottom:16px;font-size:13px;}

  @media (max-width: 768px) {
    .page-header{flex-direction:column;align-items:flex-start;gap:12px;}
    .page-header .btn-primary{width:100%;justify-content:center;}
    .goals-grid{grid-template-columns:1fr;}
    .summary-nums{flex-direction:column;gap:4px;}
    .modal-box{max-width:92vw;padding:20px;}
    .goal-actions{flex-wrap:wrap;}
  }
</style>
@endpush

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">🎯 Goals & Tabungan</div>
    <div class="page-sub">Pantau progress tabungan Anda</div>
  </div>
  <button class="btn-primary" onclick="document.getElementById('modal-add').classList.add('open')">
    + Tambah Goal
  </button>
</div>

@if(session('success'))
  <div class="alert-success">✅ {{ session('success') }}</div>
@endif

@if($goals->isNotEmpty())
<div class="summary-bar">
  <div class="summary-top">
    <div class="summary-title">Total Progress Tabungan</div>
    <div class="summary-pct">{{ $totalPercent }}%</div>
  </div>
  <div class="progress-track">
    <div class="progress-fill" style="width:{{ $totalPercent }}%"></div>
  </div>
  <div class="summary-nums">
    <span>Terkumpul: Rp {{ number_format($totalSaved, 0, ',', '.') }}</span>
    <span>Target: Rp {{ number_format($totalTarget, 0, ',', '.') }}</span>
  </div>
</div>
@endif

@if($goals->isEmpty())
  <div class="card">
    <div class="empty-state">
      <div class="icon">🎯</div>
      <div>Belum ada goal</div>
      <div style="font-size:12px;margin-top:6px">Buat goal pertama Anda dan mulai menabung!</div>
    </div>
  </div>
@else
<div class="goals-grid">
  @foreach($goals as $g)
  @php
    $pct = $g->target_amount > 0 ? min(100, round(($g->current_amount / $g->target_amount) * 100)) : 0;
  @endphp
  <div class="goal-card">
    <div class="goal-emoji">{{ $g->emoji ?? '🎯' }}</div>
    <div class="goal-title">{{ $g->title }}</div>
    @if($g->deadline)
      <div class="goal-deadline">📆 Target: {{ \Carbon\Carbon::parse($g->deadline)->format('d M Y') }}</div>
    @else
      <div class="goal-deadline" style="margin-bottom:14px"></div>
    @endif
    <div class="goal-amounts">
      <div>
        <div class="goal-saved">Rp {{ number_format($g->current_amount, 0, ',', '.') }}</div>
        <div class="goal-target">dari Rp {{ number_format($g->target_amount, 0, ',', '.') }}</div>
      </div>
      <div style="font-size:28px;font-weight:800;color:var(--text-dim)">{{ $pct }}%</div>
    </div>
    <div class="goal-progress-track">
      <div class="goal-progress-fill" style="width:{{ $pct }}%"></div>
    </div>
    <div class="goal-actions">
      <button class="btn-teal" onclick="openUpdate({{ $g->id }}, '{{ addslashes($g->title) }}', {{ $g->current_amount }})">💰 Update</button>
      <form method="POST" action="{{ route('goals.destroy', $g) }}" onsubmit="return confirm('Hapus goal ini?')">
        @csrf @method('DELETE')
        <button class="btn-danger" type="submit">Hapus</button>
      </form>
    </div>
  </div>
  @endforeach
</div>
@endif

<!-- Modal Tambah Goal -->
<div class="modal-overlay" id="modal-add" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="modal-box">
    <div class="modal-title">
      <span>Tambah Goal</span>
      <button class="modal-close" onclick="document.getElementById('modal-add').classList.remove('open')">✕</button>
    </div>
    <form method="POST" action="{{ route('goals.store') }}" id="form-add">
      @csrf
      <div class="form-group">
        <label class="form-label">Emoji</label>
        <input class="form-input" type="text" name="emoji" value="🎯" maxlength="5" style="font-size:24px;width:80px;">
      </div>
      <div class="form-group">
        <label class="form-label">Nama Goal</label>
        <input class="form-input" type="text" name="title" placeholder="Misal: Beli Laptop, Dana Darurat" required>
      </div>
      <div class="form-group">
        <label class="form-label">Target (Rp)</label>
        <div class="rp-wrap">
          <span class="rp-prefix">Rp</span>
          <input class="form-input" type="text" inputmode="numeric" id="target-display" placeholder="0" required>
        </div>
        <input type="hidden" name="target_amount" id="target-raw">
        <div class="rp-hint">Contoh: 5.000.000</div>
      </div>
      <div class="form-group">
        <label class="form-label">Target Selesai (opsional)</label>
        <input class="form-input" type="date" name="deadline">
      </div>
      <button class="btn-primary" type="submit" style="width:100%;justify-content:center;padding:12px;">Simpan Goal</button>
    </form>
  </div>
</div>

<!-- Modal Update Tabungan -->
<div class="modal-overlay" id="modal-update" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="modal-box">
    <div class="modal-title">
      <span id="update-title">Update Tabungan</span>
      <button class="modal-close" onclick="document.getElementById('modal-update').classList.remove('open')">✕</button>
    </div>
    <form method="POST" id="update-form">
      @csrf @method('PATCH')
      <div class="form-group">
        <label class="form-label">Jumlah Terkumpul Saat Ini (Rp)</label>
        <div class="rp-wrap">
          <span class="rp-prefix">Rp</span>
          <input class="form-input" type="text" inputmode="numeric" id="update-display" placeholder="0" required>
        </div>
        <input type="hidden" name="current_amount" id="update-raw">
        <div class="rp-hint">Contoh: 2.500.000</div>
      </div>
      <button class="btn-primary" type="submit" style="width:100%;justify-content:center;padding:12px;">Update Tabungan</button>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
// ── Format Rupiah Helper ──────────────────────────
function formatRp(input, hiddenId) {
  input.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    document.getElementById(hiddenId).value = value;
    e.target.value = value ? new Intl.NumberFormat('id-ID').format(value) : '';
  });
}

formatRp(document.getElementById('target-display'), 'target-raw');
formatRp(document.getElementById('update-display'), 'update-raw');

// Validasi form tambah goal
document.getElementById('form-add').addEventListener('submit', function(e) {
  if (!document.getElementById('target-raw').value) {
    e.preventDefault();
    alert('Target jumlah harus diisi');
  }
});

// Update modal
function openUpdate(id, title, current) {
  document.getElementById('update-title').textContent = '💰 Update: ' + title;
  document.getElementById('update-form').action = '/goals/' + id;

  // Format current amount
  const formatted = current > 0 ? new Intl.NumberFormat('id-ID').format(current) : '';
  document.getElementById('update-display').value = formatted;
  document.getElementById('update-raw').value = current > 0 ? current : '';

  document.getElementById('modal-update').classList.add('open');
}

// Validasi form update
document.getElementById('update-form').addEventListener('submit', function(e) {
  if (!document.getElementById('update-raw').value) {
    e.preventDefault();
    alert('Jumlah harus diisi');
  }
});
</script>
@endpush
