@extends('layouts.guest')
@section('title', 'Reset Password')

@section('content')
<div class="auth-title">Buat Password Baru 🔒</div>
<div class="auth-sub">Masukkan password baru untuk akun Anda</div>

<form method="POST" action="{{ route('password.store') }}">
  @csrf
  <input type="hidden" name="token" value="{{ $request->route('token') }}">

  <div class="form-group">
    <label class="form-label">Email</label>
    <input class="form-input" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus>
    @error('email') <div class="form-error">{{ $message }}</div> @enderror
  </div>

  <div class="form-group">
    <label class="form-label">Password Baru</label>
    <input class="form-input" type="password" name="password" placeholder="Minimal 8 karakter" required>
    @error('password') <div class="form-error">{{ $message }}</div> @enderror
  </div>

  <div class="form-group">
    <label class="form-label">Konfirmasi Password Baru</label>
    <input class="form-input" type="password" name="password_confirmation" placeholder="Ulangi password" required>
  </div>

  <button class="btn-primary" type="submit">Reset Password</button>
</form>
@endsection
