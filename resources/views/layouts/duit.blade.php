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
    --teal:#00d4aa;--amber:#f5a623;--blue:#4a9eff;--red:#ff5b5b;--green:#4cde80;
    --radius-sm:8px;--radius-md:14px;--radius-lg:20px;
}
*{box-sizing:border-box;margin:0;padding:0;}
[x-cloak]{display:none!important;}
body{font-family:'Segoe UI',system-ui,sans-serif;background:var(--bg-base);color:var(--text-main);display:flex;min-height:100vh;}

/* ── Sidebar ── */
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

/* ── Notifikasi banner ── */
.notif-banner{position:fixed;bottom:20px;right:20px;background:var(--bg-card-2);border:1px solid var(--border);border-radius:var(--radius-md);padding:14px 16px;display:flex;align-items:center;gap:12px;z-index:9998;max-width:300px;box-shadow:0 8px 32px rgba(0,0,0,0.5);}
.notif-banner-text{flex:1;font-size:12px;color:var(--text-muted);line-height:1.4;}
.notif-btn-aktif{background:var(--teal);color:#000;border:none;padding:6px 12px;border-radius:6px;font-weight:700;font-size:12px;cursor:pointer;white-space:nowrap;}
.notif-btn-dismiss{background:transparent;color:var(--text-muted);border:none;padding:4px 6px;font-size:18px;cursor:pointer;line-height:1;}

/* ── Chat AI ── */
.chat-fab{
    position:fixed;bottom:24px;right:24px;
    background:linear-gradient(135deg,var(--teal),var(--blue));
    color:#000;border:none;border-radius:24px;
    padding:12px 20px;font-size:14px;font-weight:700;
    cursor:pointer;z-index:9999;
    box-shadow:0 4px 20px rgba(0,212,170,0.4);
    display:flex;align-items:center;gap:8px;
    transition:transform .2s,box-shadow .2s;
}
.chat-fab:hover{transform:translateY(-2px);box-shadow:0 6px 28px rgba(0,212,170,0.5);}
.chat-panel{
    position:fixed;bottom:80px;right:24px;
    width:360px;height:520px;
    background:var(--bg-card);
    border:1px solid var(--border);
    border-radius:var(--radius-lg);
    display:flex;flex-direction:column;
    z-index:9999;
    box-shadow:0 16px 48px rgba(0,0,0,0.6);
    overflow:hidden;
}
.chat-header{
    padding:16px 18px;
    background:var(--bg-card-2);
    border-bottom:1px solid var(--border);
    display:flex;align-items:center;gap:10px;
}
.chat-header-avatar{
    width:32px;height:32px;border-radius:50%;
    background:linear-gradient(135deg,var(--teal),var(--blue));
    display:flex;align-items:center;justify-content:center;
    font-size:15px;flex-shrink:0;
}
.chat-header-info{flex:1;}
.chat-header-name{font-size:14px;font-weight:700;color:var(--text-main);}
.chat-header-status{font-size:11px;color:var(--teal);}
.chat-close{background:transparent;border:none;color:var(--text-muted);font-size:20px;cursor:pointer;padding:4px;line-height:1;}
.chat-close:hover{color:var(--text-main);}
.chat-messages{flex:1;overflow-y:auto;padding:16px;display:flex;flex-direction:column;gap:12px;}
.chat-messages::-webkit-scrollbar{width:3px;}
.chat-messages::-webkit-scrollbar-thumb{background:var(--border);border-radius:99px;}
.msg{max-width:85%;display:flex;flex-direction:column;gap:4px;}
.msg.user{align-self:flex-end;align-items:flex-end;}
.msg.ai{align-self:flex-start;align-items:flex-start;}
.msg-bubble{
    padding:10px 14px;border-radius:16px;
    font-size:13px;line-height:1.5;white-space:pre-wrap;word-break:break-word;
}
.msg.user .msg-bubble{background:var(--teal);color:#000;border-bottom-right-radius:4px;}
.msg.ai .msg-bubble{background:var(--bg-card-2);color:var(--text-main);border-bottom-left-radius:4px;border:1px solid var(--border);}
.msg-time{font-size:10px;color:var(--text-dim);}
.typing-bubble{background:var(--bg-card-2);border:1px solid var(--border);border-radius:16px;border-bottom-left-radius:4px;padding:12px 16px;display:flex;gap:5px;align-items:center;}
.typing-dot{width:7px;height:7px;border-radius:50%;background:var(--text-muted);animation:typing 1.2s infinite;}
.typing-dot:nth-child(2){animation-delay:.2s;}
.typing-dot:nth-child(3){animation-delay:.4s;}
@keyframes typing{0%,60%,100%{transform:translateY(0);}30%{transform:translateY(-6px);}}
.chat-input-area{padding:12px 14px;border-top:1px solid var(--border);display:flex;gap:8px;background:var(--bg-card-2);}
.chat-input{
    flex:1;background:var(--bg-input);border:1px solid var(--border);
    border-radius:var(--radius-md);padding:10px 14px;
    color:var(--text-main);font-size:13px;outline:none;
    font-family:inherit;resize:none;max-height:80px;
}
.chat-input:focus{border-color:var(--teal);}
.chat-input::placeholder{color:var(--text-dim);}
.chat-send{
    width:38px;height:38px;border-radius:50%;
    background:var(--teal);color:#000;border:none;
    cursor:pointer;display:flex;align-items:center;justify-content:center;
    font-size:16px;flex-shrink:0;align-self:flex-end;
    transition:opacity .2s;
}
.chat-send:disabled{opacity:.4;cursor:not-allowed;}

/* ── Mobile ── */
@media(max-width:768px){
    body{display:block;}
    .sidebar{width:100%;height:60px;flex-direction:row;justify-content:space-around;align-items:center;top:auto;bottom:0;left:0;right:0;padding:0 8px;border-right:none;border-top:1px solid var(--border);gap:0;}
    .sidebar-icon{width:44px;height:44px;}
    .sidebar-avatar{margin-top:0;order:5;}
    .main{margin-left:0;padding:16px;padding-bottom:80px;}
    .chat-panel{width:calc(100vw - 24px);right:12px;bottom:72px;height:70vh;}
    .chat-fab{bottom:72px;right:12px;}
    .notif-banner{bottom:72px;right:12px;max-width:calc(100vw - 24px);}
}
@media(max-width:480px){.main{padding:12px;padding-bottom:80px;}}
</style>
@stack('styles')
</head>
<body x-data="appState()" x-init="init()">

{{-- Sidebar --}}
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

{{-- Konten halaman --}}
<main class="main">
    @yield('content')
</main>

{{-- ═══════════════════════════════════════════ --}}
{{-- NOTIFIKASI BANNER                           --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="notif-banner" x-show="notifPermission === 'default'" x-cloak>
    <span style="font-size:20px;">🔔</span>
    <div class="notif-banner-text">Aktifkan notifikasi untuk pengingat jadwal &amp; alert budget</div>
    <button class="notif-btn-aktif" @click="requestNotifPermission()">Aktifkan</button>
    <button class="notif-btn-dismiss" @click="notifPermission = 'dismissed'">✕</button>
</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- CHAT AI BUTTON & PANEL                      --}}
{{-- ═══════════════════════════════════════════ --}}
<button class="chat-fab" @click="chatOpen = !chatOpen">
    <span x-text="chatOpen ? '✕ Tutup' : '✨ Tanya AI'"></span>
</button>

<div class="chat-panel" x-show="chatOpen" x-cloak
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 translate-y-4"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0 translate-y-4">

    {{-- Header --}}
    <div class="chat-header">
        <div class="chat-header-avatar">🤖</div>
        <div class="chat-header-info">
            <div class="chat-header-name">DUIT AI</div>
            <div class="chat-header-status">● Online · analisis keuangan kamu</div>
        </div>
        <button class="chat-close" @click="chatOpen = false">✕</button>
    </div>

    {{-- Messages --}}
    <div class="chat-messages" x-ref="chatMessages">
        {{-- Pesan sambutan --}}
        <div class="msg ai">
            <div class="msg-bubble">
                Halo! Saya DUIT AI 👋<br><br>
                Saya bisa bantu analisis keuangan kamu — tanyakan apapun tentang saldo, pengeluaran, atau tips hemat!
            </div>
        </div>

        {{-- Riwayat percakapan --}}
        <template x-for="(msg, i) in chatHistory" :key="i">
            <div class="msg" :class="msg.role === 'user' ? 'user' : 'ai'">
                <div class="msg-bubble" x-text="msg.content"></div>
                <div class="msg-time" x-text="msg.time"></div>
            </div>
        </template>

        {{-- Typing indicator --}}
        <div class="msg ai" x-show="chatLoading">
            <div class="typing-bubble">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
        </div>
    </div>

    {{-- Input --}}
    <div class="chat-input-area">
        <textarea class="chat-input" x-model="chatInput"
            placeholder="Tanya tentang keuangan kamu..."
            rows="1"
            @keydown.enter.prevent="if(!$event.shiftKey) sendChat()"
            @input="$el.style.height='auto'; $el.style.height=$el.scrollHeight+'px'">
        </textarea>
        <button class="chat-send" @click="sendChat()" :disabled="chatLoading || !chatInput.trim()">➤</button>
    </div>
</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- ALPINE STATE & LOGIC                        --}}
{{-- ═══════════════════════════════════════════ --}}
<script>
function appState() {
    return {
        // ── Notifikasi ──────────────────────────
        notifPermission: 'default',
        pollTimer: null,

        // ── Chat AI ─────────────────────────────
        chatOpen: false,
        chatInput: '',
        chatHistory: [],   // [{role, content, time}]
        chatLoading: false,

        // ════════════════════════════════════════
        init() {
            // Notifikasi
            if ('Notification' in window) {
                this.notifPermission = Notification.permission;
                if (this.notifPermission === 'granted') this.startPolling();
            } else {
                this.notifPermission = 'unsupported';
            }
        },

        // ── Notifikasi ──────────────────────────
        requestNotifPermission() {
            Notification.requestPermission().then(p => {
                this.notifPermission = p;
                if (p === 'granted') {
                    new Notification('DUIT 💰', { body: 'Notifikasi aktif! Pengingat jadwal & alert budget siap.' });
                    this.startPolling();
                }
            });
        },
        startPolling() {
            this.checkAlerts();
            this.pollTimer = setInterval(() => this.checkAlerts(), 60000);
        },
        checkAlerts() {
            fetch('/notifications/pending', {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf() }
            })
            .then(r => r.ok ? r.json() : Promise.reject(r.status))
            .then(d => {
                (d.alerts || []).forEach(a => {
                    const k = 'notif_' + a.id;
                    if (localStorage.getItem(k)) return;
                    new Notification(a.title, { body: a.body });
                    localStorage.setItem(k, '1');
                });
            })
            .catch(() => {});
        },

        // ── Chat AI ─────────────────────────────
        async sendChat() {
            const msg = this.chatInput.trim();
            if (!msg || this.chatLoading) return;

            this.chatInput = '';
            this.chatLoading = true;

            // Tambah pesan user ke history
            this.chatHistory.push({ role: 'user', content: msg, time: now() });
            this.$nextTick(() => this.scrollChat());

            try {
                // Kirim history (tanpa time) ke backend
                const historyPayload = this.chatHistory
                    .slice(0, -1)  // exclude pesan terakhir (sudah dikirim sebagai message)
                    .map(h => ({ role: h.role, content: h.content }));

                const res = await fetch('/ai/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf(),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ message: msg, history: historyPayload }),
                });

                const data = await res.json();

                if (data.error) {
                    this.chatHistory.push({ role: 'ai', content: '⚠️ ' + data.error, time: now() });
                } else {
                    this.chatHistory.push({ role: 'ai', content: data.reply, time: now() });
                }
            } catch (e) {
                this.chatHistory.push({ role: 'ai', content: '⚠️ Koneksi gagal. Coba lagi.', time: now() });
            }

            this.chatLoading = false;
            this.$nextTick(() => this.scrollChat());
        },

        scrollChat() {
            const el = this.$refs.chatMessages;
            if (el) el.scrollTop = el.scrollHeight;
        },
    };
}

function csrf() {
    return document.querySelector('meta[name="csrf-token"]').content;
}
function now() {
    return new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
}
</script>

@stack('scripts')
</body>
</html>
