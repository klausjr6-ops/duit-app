@extends('layouts.duit')

@section('title', 'Dashboard')

@push('styles')
<style>
  .header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:24px; }
  .header-left h1 { font-size:22px; font-weight:700; }
  .header-left .date { font-size:13px; color:var(--text-muted); margin-top:4px; }
  .financial-score {
    display:flex; align-items:center; gap:10px;
    background:var(--bg-card); border:1px solid var(--border);
    border-radius:var(--radius-md); padding:8px 14px;
  }
  .financial-score .label { font-size:12px; color:var(--text-muted); }
  .score-badge {
    background:var(--amber); color:#000;
    font-size:12px; font-weight:700; padding:3px 10px;
    border-radius:20px;
  }
  .grid-top { display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; margin-bottom:16px; }
  .grid-stats { display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; margin-bottom:16px; }
  .grid-middle { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px; }
  .card {
    background:var(--bg-card); border:1px solid var(--border);
    border-radius:var(--radius-md); padding:20px;
  }
  .card-label {
    font-size:10px; font-weight:700; letter-spacing:0.12em;
    color:var(--text-muted); text-transform:uppercase; margin-bottom:10px;
  }
  /* Clock */
  .clock-card { display:flex; flex-direction:column; justify-content:center; min-height:170px; position:relative; overflow:hidden; }
  .clock-card::after { content:''; position:absolute; bottom:0; left:0; right:0; height:3px; background:linear-gradient(90deg,var(--teal),var(--blue)); border-radius:0 0 var(--radius-md) var(--radius-md); }
  .clock-time { font-size:48px; font-weight:800; letter-spacing:-1px; }
  .clock-day { font-size:16px; font-weight:600; color:var(--teal); margin-top:10px; }
  .clock-date { font-size:13px; color:var(--text-muted); margin-top:2px; }
  /* Health Ring */
  .health-card { display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:170px; }
  .ring-container { position:relative; width:110px; height:110px; }
  .ring-svg { transform:rotate(-90deg); }
  .ring-bg { fill:none; stroke:var(--bg-input); stroke-width:10; }
  .ring-fill { fill:none; stroke:var(--teal); stroke-width:10; stroke-linecap:round; stroke-dasharray:283; stroke-dashoffset:141; transition:stroke-dashoffset 1s ease; }
  .ring-label { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); text-align:center; }
  .ring-score { font-size:28px; font-weight:800; color:var(--teal); }
  .ring-max { font-size:11px; color:var(--text-muted); }
  /* Today */
  .today-item { display:flex; align-items:center; gap:10px; padding:9px 0; border-bottom:1px solid var(--border); }
  .today-item:last-child { border-bottom:none; }
  .today-icon { width:32px; height:32px; border-radius:var(--radius-sm); background:var(--bg-input); display:flex; align-items:center; justify-content:center; font-size:14px; flex-shrink:0; }
  .today-name { flex:1; font-size:13px; }
  .today-value { font-size:14px; font-weight:700; }
  .income-text { color:var(--teal); }
  .expense-text { color:var(--red); }
  .muted-text { color:var(--text-muted); }
  /* Stat cards */
  .stat-card { position:relative; overflow:hidden; }
  .stat-card::after { content:''; position:absolute; top:0; left:0; right:0; height:3px; border-radius:var(--radius-md) var(--radius-md) 0 0; }
  .stat-card.teal::after { background:var(--teal); }
  .stat-card.amber::after { background:var(--amber); }
  .stat-card.blue::after { background:var(--blue); }
  .stat-value { font-size:28px; font-weight:800; margin-top:6px; }
  .stat-card.teal .stat-value { color:var(--teal); }
  .stat-card.amber .stat-value { color:var(--amber); }
  .stat-card.blue .stat-value { color:var(--blue); }
  .stat-sub { font-size:12px; color:var(--text-muted); margin-top:4px; }
  /* Timeline */
  .timeline-track { width:100%; height:4px; background:var(--bg-input); border-radius:99px; position:relative; margin:12px 0; }
  .timeline-fill { height:100%; background:linear-gradient(90deg,var(--teal),var(--blue)); border-radius:99px; }
  .timeline-dot { position:absolute; top:50%; transform:translate(-50%,-50%); width:14px; height:14px; background:var(--teal); border-radius:50%; box-shadow:0 0 0 4px rgba(0,212,170,0.2); }
  .timeline-empty { display:flex; flex-direction:column; align-items:center; justify-content:center; height:100px; color:var(--text-dim); font-size:13px; gap:8px; }
  /* Mood */
  .mood-buttons { display:flex; gap:8px; margin:10px 0 16px; }
  .mood-btn { flex:1; display:flex; flex-direction:column; align-items:center; gap:4px; padding:10px 6px; border-radius:var(--radius-sm); background:var(--bg-input); border:2px solid transparent; cursor:pointer; transition:all 0.2s; color:var(--text-main); }
  .mood-btn:hover { border-color:var(--border); }
  .mood-btn.active { border-color:var(--teal); background:rgba(0,212,170,0.1); }
  .mood-emoji { font-size:22px; }
  .mood-label { font-size:10px; color:var(--text-muted); }
  .catatan-label { font-size:11px; font-weight:700; letter-spacing:0.1em; color:var(--text-muted); text-transform:uppercase; margin-bottom:8px; }
  .catatan-input { width:100%; background:var(--bg-input); border:1px solid var(--border); border-radius:var(--radius-sm); padding:10px 12px; color:var(--text-main); font-size:13px; resize:none; outline:none; font-family:inherit; transition:border-color 0.2s; }
  .catatan-input:focus { border-color:var(--teal); }
  .catatan-input::placeholder { color:var(--text-dim); }
  /* Report */
  .report-inner { display:grid; grid-template-columns:200px 1fr; gap:20px; align-items:center; }
  .report-chart-wrap { width:180px; height:180px; position:relative; display:flex; align-items:center; justify-content:center; }
  .report-chart-wrap canvas { position:absolute; }
  .report-chart-center { position:relative; z-index:1; text-align:center; }
  .report-chart-pct { font-size:22px; font-weight:800; }
  .report-chart-sub { font-size:10px; color:var(--text-muted); }
  .report-rows { display:flex; flex-direction:column; gap:10px; }
  .report-row { display:flex; align-items:center; background:var(--bg-card-2,#1e2333); border-radius:var(--radius-sm); padding:12px 16px; gap:12px; }
  .report-row-icon { width:32px; height:32px; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:14px; }
  .report-row-icon.income { background:rgba(0,212,170,0.15); }
  .report-row-icon.expense { background:rgba(255,91,91,0.15); }
  .report-row-name { flex:1; font-size:13px; color:var(--text-muted); }
  .report-row-val { font-size:16px; font-weight:700; }
  .report-bar { width:100%; height:4px; background:var(--bg-input); border-radius:99px; margin-top:6px; }
  .report-bar-fill { height:100%; border-radius:99px; }
  .report-bar-fill.income { background:var(--teal); }
  .report-bar-fill.expense { background:var(--red); }
  /* FAB */
  .fab { position:fixed; bottom:24px; right:24px; background:var(--teal); color:#000; font-weight:700; font-size:14px; padding:14px 22px; border-radius:99px; border:none; cursor:pointer; box-shadow:0 6px 24px rgba(0,212,170,0.4); display:flex; align-items:center; gap:8px; transition:transform 0.2s; z-index:200; }
  .fab:hover { transform:translateY(-2px); }
  /* Modal */
  .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:300; align-items:flex-end; justify-content:center; }
  .modal-overlay.open { display:flex; }
  .modal-box { width:100%; max-width:560px; background:var(--bg-card); border-radius:var(--radius-lg) var(--radius-lg) 0 0; border:1px solid var(--border); padding:24px; animation:slideUp 0.3s ease; }
  @keyframes slideUp { from{transform:translateY(100px);opacity:0} to{transform:translateY(0);opacity:1} }
  .modal-title { font-size:16px; font-weight:700; display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; }
  .modal-close { background:var(--bg-input); border:none; color:var(--text-muted); width:30px; height:30px; border-radius:50%; font-size:16px; cursor:pointer; display:flex; align-items:center; justify-content:center; }
  .modal-messages { height:200px; overflow-y:auto; display:flex; flex-direction:column; gap:10px; margin-bottom:14px; }
  .msg-bubble { padding:10px 14px; border-radius:12px; font-size:13px; max-width:85%; }
  .msg-bubble.ai { background:var(--bg-input); color:var(--text-main); align-self:flex-start; }
  .msg-bubble.user { background:var(--teal); color:#000; align-self:flex-end; }
  .modal-input-row { display:flex; gap:10px; }
  .modal-input { flex:1; background:var(--bg-input); border:1px solid var(--border); border-radius:var(--radius-sm); padding:10px 14px; color:var(--text-main); font-size:13px; outline:none; font-family:inherit; }
  .modal-send { background:var(--teal); color:#000; border:none; border-radius:var(--radius-sm); padding:10px 18px; font-weight:700; font-size:13px; cursor:pointer; }

@media (max-width: 768px) {
  .header{flex-direction:column;align-items:flex-start;gap:12px;}
  .grid-top{grid-template-columns:1fr;}
  .grid-stats{grid-template-columns:1fr;}
  .grid-middle{grid-template-columns:1fr;}
  .report-inner{grid-template-columns:1fr;}
  .report-chart-wrap{margin:0 auto;}
  .clock-time{font-size:36px;}
  .stat-value{font-size:24px;}
}

@media (max-width: 480px) {
  .clock-time{font-size:30px;}
  .mood-buttons{flex-wrap:wrap;}
  .mood-btn{flex:0 0 calc(33.333% - 6px);}
}

@media (max-width: 768px) {
  .fab{
    bottom:76px;
    right:16px;
    padding:12px 18px;
    font-size:13px;
  }
  .modal-overlay{
    align-items:flex-end;
  }
  .modal-box{
    max-height:80vh;
    overflow-y:auto;
  }
}

</style>
@endpush

@section('content')
<!-- Header -->
<div class="header">
  <div class="header-left">
    <h1 id="greeting">Selamat sore 🌅</h1>
    <div class="date" id="full-date">{{ now()->translatedFormat('l, d F Y') }}</div>
  </div>
  <div class="financial-score">
    <span class="label">Skor keuangan</span>
    <div class="score-badge">
      @if($financialScore >= 80) Sangat Baik 🟢
      @elseif($financialScore >= 60) Baik 🔵
      @elseif($financialScore >= 40) Cukup 🟡
      @else Perlu Perhatian 🔴
      @endif
    </div>
  </div>
</div>

<!-- Row 1: Clock | Health | Today -->
<div class="grid-top">
  <div class="card clock-card">
    <div class="clock-time" id="clock">{{ now()->format('H:i:s') }}</div>
    <div class="clock-day" id="clock-day">{{ now()->translatedFormat('l') }}</div>
    <div class="clock-date" id="clock-date">{{ now()->translatedFormat('d F Y') }}</div>
  </div>

  <div class="card health-card">
    <div class="card-label">Kesehatan Keuangan</div>
    <div class="ring-container">
      <svg class="ring-svg" width="110" height="110" viewBox="0 0 110 110">
        <circle class="ring-bg" cx="55" cy="55" r="45"/>
        <circle class="ring-fill" id="health-ring" cx="55" cy="55" r="45"/>
      </svg>
      <div class="ring-label">
        <div class="ring-score">{{ $financialScore }}</div>
        <div class="ring-max">/100</div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-label">Hari Ini</div>
    <div class="today-item">
      <div class="today-icon">⬇️</div>
      <span class="today-name">Masuk Hari Ini</span>
      <span class="today-value income-text">Rp {{ number_format($todayIncome, 0, ',', '.') }}</span>
    </div>
    <div class="today-item">
      <div class="today-icon">⬆️</div>
      <span class="today-name">Keluar Hari ini</span>
      <span class="today-value expense-text">Rp {{ number_format($todayExpense, 0, ',', '.') }}</span>
    </div>
    <div class="today-item">
      <div class="today-icon">📅</div>
      <span class="today-name">Jadwal aktif</span>
      <span class="today-value muted-text">{{ $activeSchedules }} kegiatan</span>
    </div>
  </div>
</div>

<!-- Row 2: Stats -->
<div class="grid-stats">
  <div class="card stat-card teal">
    <div class="card-label">Total Saldo</div>
    <div class="stat-value">Rp {{ number_format($totalBalance, 0, ',', '.') }}</div>
  </div>
  <div class="card stat-card amber">
    <div class="card-label">Pengeluaran Bulan Ini</div>
    <div class="stat-value">Rp {{ number_format($monthlyExpense, 0, ',', '.') }}</div>
  </div>
  <div class="card stat-card blue">
    <div class="card-label">Tabungan Terkumpul</div>
    <div class="stat-value">Rp {{ number_format($totalSavings, 0, ',', '.') }}</div>
    <div class="stat-sub">dari seluruh goals</div>
  </div>
</div>

<!-- Row 3: Timeline | Mood -->
<div class="grid-middle">
  <div class="card">
    <div class="card-label">Timeline Hari Ini</div>
    <div class="timeline-track">
      <div class="timeline-fill" id="timeline-fill" style="width:0%"></div>
      <div class="timeline-dot" id="timeline-dot" style="left:0%"></div>
    </div>
    @if($todaySchedules->isEmpty())
      <div class="timeline-empty">
        <span style="font-size:32px">📅</span>
        Belum ada jadwal hari ini
      </div>
    @else
      @foreach($todaySchedules as $schedule)
        <div style="padding:8px 0; border-bottom:1px solid var(--border); font-size:13px; display:flex; justify-content:space-between;">
          <span>{{ $schedule->title }}</span>
          <span style="color:var(--text-muted)">{{ $schedule->time }}</span>
        </div>
      @endforeach
    @endif
  </div>

  <div class="card">
    <div class="card-label">Suasana Hati Hari Ini</div>
    <div class="mood-buttons">
      @foreach([['ngantuk','🥱','Ngantuk'],['lesu','😐','Lesu'],['biasa','😶','Biasa'],['baik','🙂','Baik'],['semangat','🔥','Semangat']] as $m)
      <button class="mood-btn {{ $todayMood == $m[0] ? 'active' : '' }}" data-mood="{{ $m[0] }}" onclick="setMood(this)">
        <span class="mood-emoji">{{ $m[1] }}</span>
        <span class="mood-label">{{ $m[2] }}</span>
      </button>
      @endforeach
    </div>
    <div class="catatan-label">Catatan Singkat</div>
    <textarea class="catatan-input" rows="3" placeholder="Apa yang terjadi hari ini..." id="daily-note">{{ $todayNote }}</textarea>
    <button onclick="saveNote()" style="margin-top:8px; background:var(--teal); color:#000; border:none; border-radius:var(--radius-sm); padding:8px 16px; font-weight:700; font-size:12px; cursor:pointer; width:100%;">Simpan Catatan</button>
  </div>
</div>

<!-- Row 4: Laporan Harian -->
<div class="card" style="margin-bottom:16px">
  <div class="card-label">Laporan Harian</div>
  <div class="report-inner">
    <div class="report-chart-wrap">
      <canvas id="donutChart" width="180" height="180"></canvas>
      <div class="report-chart-center">
        <div class="report-chart-pct">{{ $savingRate }}%</div>
        <div class="report-chart-sub">tabungan</div>
      </div>
    </div>
    <div class="report-rows">
      <div class="report-row">
        <div class="report-row-icon income">⬇️</div>
        <div style="flex:1">
          <div style="display:flex;justify-content:space-between;align-items:center">
            <span class="report-row-name">Pemasukan</span>
            <span class="report-row-val income-text">Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</span>
          </div>
          <div class="report-bar"><div class="report-bar-fill income" style="width:{{ $incomeBarPct }}%"></div></div>
        </div>
      </div>
      <div class="report-row">
        <div class="report-row-icon expense">⬆️</div>
        <div style="flex:1">
          <div style="display:flex;justify-content:space-between;align-items:center">
            <span class="report-row-name">Pengeluaran</span>
            <span class="report-row-val expense-text">Rp {{ number_format($monthlyExpense, 0, ',', '.') }}</span>
          </div>
          <div class="report-bar"><div class="report-bar-fill expense" style="width:{{ $expenseBarPct }}%"></div></div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
const dayNames = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
const monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

function updateClock() {
  const now = new Date();
  const h = String(now.getHours()).padStart(2,'0');
  const m = String(now.getMinutes()).padStart(2,'0');
  const s = String(now.getSeconds()).padStart(2,'0');
  document.getElementById('clock').textContent = `${h}:${m}:${s}`;
  document.getElementById('clock-day').textContent = dayNames[now.getDay()];
  document.getElementById('clock-date').textContent = `${now.getDate()} ${monthNames[now.getMonth()]} ${now.getFullYear()}`;
  document.getElementById('full-date').textContent = `${dayNames[now.getDay()]}, ${now.getDate()} ${monthNames[now.getMonth()]} ${now.getFullYear()}`;

  const h24 = now.getHours();
  let greet = h24 < 12 ? 'Selamat pagi 🌤️' : h24 < 15 ? 'Selamat siang ☀️' : h24 < 18 ? 'Selamat sore 🌅' : 'Selamat malam 🌙';
  document.getElementById('greeting').textContent = greet;

  const pct = Math.min(100, ((now.getHours()*60+now.getMinutes()) / 1440) * 100);
  document.getElementById('timeline-fill').style.width = pct + '%';
  document.getElementById('timeline-dot').style.left = pct + '%';
}
setInterval(updateClock, 1000);
updateClock();

// Health ring
const score = {{ $financialScore }};
const circumference = 2 * Math.PI * 45;
document.getElementById('health-ring').style.strokeDashoffset = circumference - (score/100) * circumference;

// Donut chart
const ctx = document.getElementById('donutChart').getContext('2d');
new Chart(ctx, {
  type: 'doughnut',
  data: {
    labels: ['Pemasukan','Pengeluaran','Sisa'],
    datasets: [{
      data: [{{ $monthlyIncome }}, {{ $monthlyExpense }}, {{ max(0, $monthlyIncome - $monthlyExpense) }}],
      backgroundColor: ['#00d4aa','#ff5b5b','#4a9eff'],
      borderWidth: 0,
    }]
  },
  options: {
    cutout: '72%',
    plugins: { legend: { display: false }, tooltip: { enabled: true } },
  }
});

// Mood
function setMood(el) {
  document.querySelectorAll('.mood-btn').forEach(b => b.classList.remove('active'));
  el.classList.add('active');
  fetch('{{ route("dashboard.mood") }}', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
    body: JSON.stringify({ mood: el.dataset.mood })
  });
}

// Save note
function saveNote() {
  const note = document.getElementById('daily-note').value;
  fetch('{{ route("dashboard.note") }}', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
    body: JSON.stringify({ note })
  }).then(() => { alert('Catatan tersimpan!'); });
}
</script>

<!-- FAB -->
<button class="fab" onclick="document.getElementById('ai-modal').classList.add('open')">✨ Tanya AI</button>

<div class="modal-overlay" id="ai-modal" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="modal-box">
    <div class="modal-title">
      <span>✨ Tanya AI Keuangan</span>
      <button class="modal-close" onclick="document.getElementById('ai-modal').classList.remove('open')">✕</button>
    </div>
    <div class="modal-messages" id="modal-msgs">
      <div class="msg-bubble ai">Halo! Saya asisten keuangan Anda. Ada yang bisa saya bantu? 💰</div>
    </div>
    <div class="modal-input-row">
      <input class="modal-input" id="modal-input" placeholder="Tanya soal keuangan Anda..." onkeydown="if(event.key==='Enter')sendMsg()"/>
      <button class="modal-send" onclick="sendMsg()">Kirim</button>
    </div>
  </div>
</div>

<script>
function sendMsg() {
  const input = document.getElementById('modal-input');
  const msgs = document.getElementById('modal-msgs');
  const text = input.value.trim();
  if (!text) return;
  const userBubble = document.createElement('div');
  userBubble.className = 'msg-bubble user';
  userBubble.textContent = text;
  msgs.appendChild(userBubble);
  input.value = '';
  msgs.scrollTop = msgs.scrollHeight;

  fetch('{{ route("dashboard.ai") }}', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
    body: JSON.stringify({ message: text })
  })
  .then(r => r.json())
  .then(data => {
    const aiBubble = document.createElement('div');
    aiBubble.className = 'msg-bubble ai';
    aiBubble.textContent = data.reply;
    msgs.appendChild(aiBubble);
    msgs.scrollTop = msgs.scrollHeight;
  });
}
</script>
@endpush
