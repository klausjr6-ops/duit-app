@extends('layouts.guest')
@section('title', 'Masuk')

@section('content')
<div class="auth-title">Selamat Datang Kembali 👋</div>
<div class="auth-sub">Masuk untuk melanjutkan ke dashboard keuangan Anda</div>

@if(session('status'))
  <div class="form-status">{{ session('status') }}</div>
@endif

<form method="POST" action="{{ route('login') }}">
  @csrf

  <div class="form-group">
    <label class="form-label">Email</label>
    <input class="form-input" type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus>
    @error('email') <div class="form-error">{{ $message }}</div> @enderror
  </div>

  <div class="form-group">
    <label class="form-label">Password</label>
    <input class="form-input" type="password" name="password" placeholder="••••••••" required>
  </div>

  <div class="checkbox-row">
    <input type="checkbox" name="remember" id="remember">
    <label for="remember">Ingat saya</label>
  </div>

  <button class="btn-primary" type="submit">Masuk</button>

  <div class="auth-footer">
    <span class="auth-muted">Belum punya akun?</span>
    <a class="auth-link" href="{{ route('register') }}">Daftar di sini</a>
  </div>

  @if (Route::has('password.request'))
    <div style="text-align:center;margin-top:14px;">
      <a class="auth-link" href="{{ route('password.request') }}" style="font-size:12px;color:var(--text-muted);">Lupa password?</a>
    </div>
  @endif
</form>
@endsection
