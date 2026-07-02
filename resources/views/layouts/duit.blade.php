<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>DUIT — @yield('title', 'Dashboard')</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
<style>
:root {
--bg-base:#0f1117;--bg-card:#181c27;--bg-card-2:#1e2333;--bg-input:#232840;
--border:#2a2f45;--text-main:#e8eaf0;--text-muted:#7880a0;--text-dim:#4a5070;
--teal:#00d4aa;--amber:#f5a623;--blue:#4a9eff;--red:#ff5b5b;
--radius-sm:8px;--radius-md:14px;--radius-lg:20px;
}
*{box-sizing:border-box;margin:0;padding:0;}
[x-cloak]{display:none!important;}
body{font-family:'Segoe UI',system-ui,sans-serif;background:var(--bg-base);color:var(--text-main);display:flex;min-height:100vh;}
/* Desktop sidebar */
.sidebar{width:64px;background:var(--bg-card);border-right:1px solid var(--border);display:flex;flex-direction:column;align-items:center;padding:16px 0;gap:6px;position:fixed;top:0;left:0;bottom:0;z-index:100;}
.sidebar-icon{width:44px;height:44px;border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;font-size:20px;cursor:pointer;transition:background 0.2s;text-decoration:none;}
.sidebar-icon:hover,.sidebar-icon.active{background:var(--bg-card-2);}
.sidebar-avatar{margin-top:auto;width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,var(--teal),var(--blue));display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;cursor:pointer;color:#000;overflow:hidden;text-decoration:none;border:2px solid transparent;transition:border-color .2s;}
.sidebar-avatar:hover,.sidebar-avatar.active{border-color:var(--teal);}
.sidebar-avatar img{width:100%;height:100%;object-fit:cover;}
.main{margin-left:64px;flex:1;padding:24px 28px;min-width:0;}
::-webkit-scrollbar{width:5px;}
::-webkit-scrollbar-track{background:var(--bg-base);}
::-webkit-scrollbar-thumb{background:var(--border);border-radius:99px;}
/* Notifikasi banner */
.notif-banner{
    position:fixed;bottom:20px;right:20px;
    background:var(--bg-card-2);
    border:1px solid var(--border);
    border-radius:var(--radius-md);
    padding:14px 16px;
    display:flex;align-items:center;gap:12px;
    z-index:9999;max-width:300px;
    box-shadow:0 8px 32px rgba(0,0,0,0.5);
}
.notif-banner-text{flex:1;font-size:12px;color:var(--text-muted);line-height:1.4;}
.notif-btn-aktif{background:var(--teal);color:#000;border:none;padding:6px 12px;border-radius:6px;font-weight:700;font-size:12px;cursor:pointer;white-space:nowrap;}
.notif-btn-dismiss{background:transparent;color:var(--text-muted);border:none;padding:4px 6px;font-size:18px;cursor:pointer;line-height:1;}
.notif-btn-dismiss:hover{color:var(--text-main);}
/* Mobile */
@media (max-width: 768px) {
body{display:block;}
.sidebar{width:100%;height:60px;flex-direction:row;justify-content:space-around;align-items:center;top:auto;bottom:0;left:0;right:0;padding:0 8px;border-right:none;border-top:1px solid var(--border);gap:0;}
.sidebar-icon{width:44px;height:44px;}
.sidebar-avatar{margin-top:0;order:5;}
.main{margin-left:0;padding:16px;padding-bottom:80px;}
.notif-banner{bottom:72px;right:12px;max-width:calc(100vw - 24px);}
}
@media (max-width: 480px) {
.main{padding:12px;padding-bottom:80px;}
}
</style>
@stack('styles')
</head>
<body x-data="notificationSystem()" x-init="init()">

<nav class="sidebar">
    <a class="sidebar-icon {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" title="Dashboard">🏠</a>
    <a class="sidebar-icon {{ request()->routeIs('transactions*') ? 'active' : '' }}" href="{{ route('transactions.index') }}" title="Keuangan">💰</a>
    <a class="sidebar-icon {{ request()->routeIs('schedules*') ? 'active' : '' }}" href="{{ route('schedules.index') }}" title="Jadwal">📅</a>
    <a class="sidebar-icon {{ request()->routeIs('goals*') ? 'active' : '' }}" href="{{ route('goals.index') }}" title="Goals">🎯</a>
    <a class="sidebar-avatar {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}" title="Profil">
        @if(auth()->user()->avatar ?? false)
            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar">
        @else
            {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
        @endif
    </a>
</nav>

<main class="main">
    @yield('content')
</main>

{{-- Banner minta izin notifikasi --}}
<div class="notif-banner" x-show="permission === 'default'" x-cloak>
    <span style="font-size:20px;">🔔</span>
    <div class="notif-banner-text">
        Aktifkan notifikasi untuk pengingat jadwal &amp; alert budget
    </div>
    <button class="notif-btn-aktif" @click="requestPermission()">Aktifkan</button>
    <button class="notif-btn-dismiss" @click="permission = 'dismissed'">✕</button>
</div>

<script>
function notificationSystem() {
    return {
        permission: 'default',
        pollTimer: null,
        pollIntervalMs: 60000,

        init() {
            if (!('Notification' in window)) {
                this.permission = 'unsupported';
                return;
            }
            this.permission = Notification.permission;
            if (this.permission === 'granted') {
                this.startPolling();
            }
        },

        requestPermission() {
            Notification.requestPermission().then(perm => {
                this.permission = perm;
                if (perm === 'granted') {
                    new Notification('DUIT 💰', {
                        body: 'Notifikasi aktif! Kamu akan dapat pengingat jadwal & alert budget.',
                    });
                    this.startPolling();
                }
            });
        },

        startPolling() {
            this.checkAlerts();
            this.pollTimer = setInterval(() => this.checkAlerts(), this.pollIntervalMs);
        },

        checkAlerts() {
            fetch('/notifications/pending', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(res => { if (!res.ok) throw new Error(res.status); return res.json(); })
            .then(data => {
                (data.alerts || []).forEach(alert => {
                    const key = 'notif_shown_' + alert.id;
                    if (localStorage.getItem(key)) return;
                    new Notification(alert.title, { body: alert.body });
                    localStorage.setItem(key, '1');
                });
            })
            .catch(() => {});
        }
    };
}
</script>

@stack('scripts')
</body>
</html>
