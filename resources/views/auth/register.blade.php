@extends('layouts.guest')
@section('title', 'Daftar')

@section('content')
<div class="auth-title">Buat Akun Baru 🚀</div>
<div class="auth-sub">Mulai kelola keuangan Anda dengan lebih baik</div>

<form method="POST" action="{{ route('register') }}">
  @csrf

  <div class="form-group">
    <label class="form-label">Nama Lengkap</label>
    <input class="form-input" type="text" name="name" value="{{ old('name') }}" placeholder="Nama Anda" required autofocus>
    @error('name') <div class="form-error">{{ $message }}</div> @enderror
  </div>

  <div class="form-group">
    <label class="form-label">Email</label>
    <input class="form-input" type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required>
    @error('email') <div class="form-error">{{ $message }}</div> @enderror
  </div>

  <div class="form-group">
    <label class="form-label">Password</label>
    <input class="form-input" type="password" name="password" placeholder="Minimal 8 karakter" required>
    @error('password') <div class="form-error">{{ $message }}</div> @enderror
  </div>

  <div class="form-group">
    <label class="form-label">Konfirmasi Password</label>
    <input class="form-input" type="password" name="password_confirmation" placeholder="Ulangi password" required>
  </div>

  <button class="btn-primary" type="submit">Daftar Sekarang</button>

  <div class="auth-footer">
    <span class="auth-muted">Sudah punya akun?</span>
    <a class="auth-link" href="{{ route('login') }}">Masuk di sini</a>
  </div>
</form>
@endsection
