@extends('layouts.duit')
@section('title', 'Jadwal')

@push('styles')
<style>
  .page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;}
  .page-title{font-size:22px;font-weight:700;}
  .page-sub{font-size:13px;color:var(--text-muted);margin-top:4px;}
  .grid-stats{display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin-bottom:24px;}
  .card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);padding:20px;}
  .card-label{font-size:10px;font-weight:700;letter-spacing:.12em;color:var(--text-muted);text-transform:uppercase;margin-bottom:8px;}
  .stat-value{font-size:32px;font-weight:800;}
  .teal{color:var(--teal)}.blue{color:var(--blue)}
  .card.teal-top{border-top:3px solid var(--teal);}
  .card.blue-top{border-top:3px solid var(--blue);}
  .btn-primary{background:var(--teal);color:#000;border:none;border-radius:var(--radius-sm);padding:10px 20px;font-weight:700;font-size:13px;cursor:pointer;display:flex;align-items:center;gap:6px;}
  .btn-danger{background:rgba(255,91,91,.15);color:var(--red);border:1px solid rgba(255,91,91,.3);border-radius:var(--radius-sm);padding:6px 12px;font-size:12px;cursor:pointer;}
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
  .schedule-list{display:flex;flex-direction:column;gap:12px;}
  .schedule-item{display:flex;align-items:center;gap:14px;background:var(--bg-card-2,#1e2333);border-radius:var(--radius-sm);padding:14px 16px;}
  .schedule-date{text-align:center;min-width:48px;}
  .schedule-date .day{font-size:22px;font-weight:800;color:var(--teal);line-height:1;}
  .schedule-date .month{font-size:10px;color:var(--text-muted);text-transform:uppercase;}
  .schedule-info{flex:1;}
  .schedule-title{font-size:14px;font-weight:600;}
  .schedule-time{font-size:12px;color:var(--text-muted);margin-top:2px;}
  .schedule-desc{font-size:12px;color:var(--text-dim);margin-top:4px;}
  .today-badge{font-size:10px;background:rgba(0,212,170,.15);color:var(--teal);padding:2px 8px;border-radius:99px;font-weight:700;}
  .empty-state{text-align:center;padding:60px 20px;color:var(--text-dim);}
  .empty-state .icon{font-size:48px;margin-bottom:12px;}
  .alert-success{background:rgba(0,212,170,.1);border:1px solid rgba(0,212,170,.3);color:var(--teal);padding:12px 16px;border-radius:var(--radius-sm);margin-bottom:16px;font-size:13px;}

@media (max-width: 768px) {
  .page-header{flex-direction:column;align-items:flex-start;gap:12px;}
  .page-header .btn-primary{width:100%;justify-content:center;}
  .grid-stats{grid-template-columns:1fr;}
  .schedule-item{flex-wrap:wrap;}
  .schedule-info{min-width:0;flex:1 1 100%;}
  .modal-box{max-width:92vw;padding:20px;}
}

@media (max-width: 480px) {
  .page-title{font-size:18px;}
  .stat-value{font-size:24px;}
  .schedule-item{padding:12px;}
}

</style>
@endpush

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">📅 Jadwal</div>
    <div class="page-sub">Kelola kegiatan dan agenda Anda</div>
  </div>
  <button class="btn-primary" onclick="document.getElementById('modal-add').classList.add('open')">
    + Tambah Jadwal
  </button>
</div>

@if(session('success'))
  <div class="alert-success">✅ {{ session('success') }}</div>
@endif

<div class="grid-stats">
  <div class="card teal-top">
    <div class="card-label">Jadwal Hari Ini</div>
    <div class="stat-value teal">{{ $todayCount }}</div>
  </div>
  <div class="card blue-top">
    <div class="card-label">Jadwal Mendatang</div>
    <div class="stat-value blue">{{ $upcomingCount }}</div>
  </div>
</div>

<div class="card">
  @if($schedules->isEmpty())
    <div class="empty-state">
      <div class="icon">📅</div>
      <div>Belum ada jadwal</div>
      <div style="font-size:12px;margin-top:6px">Klik "Tambah Jadwal" untuk membuat kegiatan</div>
    </div>
  @else
    <div class="schedule-list">
      @foreach($schedules as $s)
      <div class="schedule-item">
        <div class="schedule-date">
          <div class="day">{{ \Carbon\Carbon::parse($s->date)->format('d') }}</div>
          <div class="month">{{ \Carbon\Carbon::parse($s->date)->format('M') }}</div>
        </div>
        <div class="schedule-info">
          <div class="schedule-title">
            {{ $s->title }}
            @if(\Carbon\Carbon::parse($s->date)->isToday())
              <span class="today-badge">Hari Ini</span>
            @endif
          </div>
          @if($s->time)
            <div class="schedule-time">🕐 {{ \Carbon\Carbon::parse($s->time)->format('H:i') }}</div>
          @endif
          @if($s->description)
            <div class="schedule-desc">{{ $s->description }}</div>
          @endif
        </div>
        <form method="POST" action="{{ route('schedules.destroy', $s) }}" onsubmit="return confirm('Hapus jadwal ini?')">
          @csrf @method('DELETE')
          <button class="btn-danger" type="submit">Hapus</button>
        </form>
      </div>
      @endforeach
    </div>
    <div style="margin-top:16px">{{ $schedules->links() }}</div>
  @endif
</div>

<!-- Modal -->
<div class="modal-overlay" id="modal-add" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="modal-box">
    <div class="modal-title">
      <span>Tambah Jadwal</span>
      <button class="modal-close" onclick="document.getElementById('modal-add').classList.remove('open')">✕</button>
    </div>
    <form method="POST" action="{{ route('schedules.store') }}">
      @csrf
      <div class="form-group">
        <label class="form-label">Judul Kegiatan</label>
        <input class="form-input" type="text" name="title" placeholder="Misal: Meeting dengan klien" required>
      </div>
      <div class="form-group">
        <label class="form-label">Tanggal</label>
        <input class="form-input" type="date" name="date" value="{{ date('Y-m-d') }}" required>
      </div>
      <div class="form-group">
        <label class="form-label">Jam (opsional)</label>
        <input class="form-input" type="time" name="time">
      </div>
      <div class="form-group">
        <label class="form-label">Keterangan (opsional)</label>
        <input class="form-input" type="text" name="description" placeholder="Tambahkan detail...">
      </div>
      <button class="btn-primary" type="submit" style="width:100%;justify-content:center;padding:12px;">Simpan Jadwal</button>
    </form>
  </div>
</div>
@endsection
