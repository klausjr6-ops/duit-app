<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>DUIT — @yield('title', 'Dashboard')</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
  <style>
    :root {
      --bg-base:     #0f1117;
      --bg-card:     #181c27;
      --bg-card-2:   #1e2333;
      --bg-input:    #232840;
      --border:      #2a2f45;
      --text-main:   #e8eaf0;
      --text-muted:  #7880a0;
      --text-dim:    #4a5070;
      --teal:        #00d4aa;
      --amber:       #f5a623;
      --blue:        #4a9eff;
      --red:         #ff5b5b;
      --green:       #4cde80;
      --radius-sm:   8px;
      --radius-md:   14px;
      --radius-lg:   20px;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Segoe UI', system-ui, sans-serif;
      background: var(--bg-base);
      color: var(--text-main);
      display: flex; min-height: 100vh;
    }
    /* Sidebar */
    .sidebar {
      width: 64px; background: var(--bg-card);
      border-right: 1px solid var(--border);
      display: flex; flex-direction: column;
      align-items: center; padding: 16px 0; gap: 6px;
      position: fixed; top: 0; left: 0; bottom: 0; z-index: 100;
    }
    .sidebar-icon {
      width: 44px; height: 44px; border-radius: var(--radius-sm);
      display: flex; align-items: center; justify-content: center;
      font-size: 20px; cursor: pointer; transition: background 0.2s;
      text-decoration: none;
    }
    .sidebar-icon:hover, .sidebar-icon.active { background: var(--bg-card-2); }
    .sidebar-avatar {
      margin-top: auto; width: 38px; height: 38px; border-radius: 50%;
      background: linear-gradient(135deg, var(--teal), var(--blue));
      display: flex; align-items: center; justify-content: center;
      font-size: 16px; font-weight: 700; cursor: pointer; color: #000;
    }
    .main { margin-left: 64px; flex: 1; padding: 24px 28px; }
    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-track { background: var(--bg-base); }
    ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 99px; }
  </style>
  @stack('styles')
</head>
<body>
<nav class="sidebar">
  <a class="sidebar-icon {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" title="Dashboard">🏠</a>
  <a class="sidebar-icon {{ request()->routeIs('transactions*') ? 'active' : '' }}" href="{{ route('transactions.index') }}" title="Keuangan">💰</a>
  <a class="sidebar-icon {{ request()->routeIs('schedules*') ? 'active' : '' }}" href="{{ route('schedules.index') }}" title="Jadwal">📅</a>
  <a class="sidebar-icon {{ request()->routeIs('goals*') ? 'active' : '' }}" href="{{ route('goals.index') }}" title="Goals">🎯</a>
  <div class="sidebar-avatar">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</div>
</nav>

<main class="main">
  @yield('content')
</main>

@stack('scripts')
</body>
</html>
