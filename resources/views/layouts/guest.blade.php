<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>DUIT — @yield('title', 'Masuk')</title>
  <style>
    :root {
      --bg-base:#0f1117;--bg-card:#181c27;--bg-input:#232840;
      --border:#2a2f45;--text-main:#e8eaf0;--text-muted:#7880a0;--text-dim:#4a5070;
      --teal:#00d4aa;--amber:#f5a623;--blue:#4a9eff;--red:#ff5b5b;
      --radius-sm:8px;--radius-md:14px;--radius-lg:20px;
    }
    *{box-sizing:border-box;margin:0;padding:0;}
    body{
      font-family:'Segoe UI',system-ui,sans-serif;
      background:var(--bg-base);
      color:var(--text-main);
      min-height:100vh;
      display:flex;
      align-items:center;
      justify-content:center;
      padding:20px;
      position:relative;
      overflow:hidden;
    }
    body::before{
      content:'';
      position:absolute;
      top:-200px;left:-100px;
      width:500px;height:500px;
      background:radial-gradient(circle,rgba(0,212,170,0.12),transparent 70%);
      border-radius:50%;
    }
    body::after{
      content:'';
      position:absolute;
      bottom:-200px;right:-100px;
      width:500px;height:500px;
      background:radial-gradient(circle,rgba(74,158,255,0.1),transparent 70%);
      border-radius:50%;
    }

    .auth-wrap{
      width:100%;
      max-width:420px;
      position:relative;
      z-index:1;
    }
    .auth-logo{
      display:flex;
      align-items:center;
      justify-content:center;
      gap:14px;
      margin-bottom:32px;
    }
    .auth-logo-icon{
      width:48px;height:48px;
      background:linear-gradient(135deg,var(--teal),var(--blue));
      border-radius:var(--radius-sm);
      display:flex;align-items:center;justify-content:center;
      font-size:24px;
      font-weight:800;
      color:#0f1117;
      flex-shrink:0;
    }
    .auth-logo-text{
      font-size:30px;font-weight:800;letter-spacing:2px;
      color:var(--text-main);
    }
    .auth-card{
      background:var(--bg-card);
      border:1px solid var(--border);
      border-radius:var(--radius-lg);
      padding:32px;
      box-shadow:0 20px 60px rgba(0,0,0,0.4);
    }
    .auth-title{font-size:20px;font-weight:700;margin-bottom:6px;}
    .auth-sub{font-size:13px;color:var(--text-muted);margin-bottom:24px;}
    .form-group{margin-bottom:16px;}
    .form-label{font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;display:block;}
    .form-input{
      width:100%;
      background:var(--bg-input);
      border:1px solid var(--border);
      border-radius:var(--radius-sm);
      padding:12px 14px;
      color:var(--text-main);
      font-size:14px;
      outline:none;
      font-family:inherit;
      transition:border-color .2s;
    }
    .form-input:focus{border-color:var(--teal);}
    .form-input::placeholder{color:var(--text-dim);}
    .form-error{font-size:12px;color:var(--red);margin-top:6px;}
    .form-status{
      background:rgba(0,212,170,.1);
      border:1px solid rgba(0,212,170,.3);
      color:var(--teal);
      padding:10px 14px;
      border-radius:var(--radius-sm);
      font-size:13px;
      margin-bottom:20px;
    }
    .checkbox-row{
      display:flex;
      align-items:center;
      gap:8px;
      margin-bottom:20px;
    }
    .checkbox-row input{
      width:16px;height:16px;
      accent-color:var(--teal);
      cursor:pointer;
    }
    .checkbox-row label{
      font-size:13px;
      color:var(--text-muted);
      cursor:pointer;
    }
    .btn-primary{
      width:100%;
      background:var(--teal);
      color:#000;
      border:none;
      border-radius:var(--radius-sm);
      padding:13px;
      font-weight:700;
      font-size:14px;
      cursor:pointer;
      transition:opacity .2s, transform .1s;
    }
    .btn-primary:hover{opacity:.9;}
    .btn-primary:active{transform:scale(0.98);}
    .auth-footer{
      display:flex;
      justify-content:space-between;
      align-items:center;
      margin-top:18px;
      font-size:13px;
    }
    .auth-link{
      color:var(--teal);
      text-decoration:none;
      font-weight:600;
    }
    .auth-link:hover{text-decoration:underline;}
    .auth-muted{color:var(--text-muted);}
    .auth-bottom-text{
      text-align:center;
      margin-top:24px;
      font-size:13px;
      color:var(--text-muted);
    }
  </style>
  @stack('styles')
</head>
<body>
  <div class="auth-wrap">
    <div class="auth-logo">
      <div class="auth-logo-icon">D</div>
      <div class="auth-logo-text">DUIT</div>
    </div>
    <div class="auth-card">
      @yield('content')
    </div>
    @hasSection('bottom')
      <div class="auth-bottom-text">@yield('bottom')</div>
    @endif
  </div>
</body>
</html>
