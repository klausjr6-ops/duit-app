@extends('layouts.guest')
@section('title', 'Lupa Password')

@section('content')
<div class="auth-title">Lupa Password? 🔑</div>
<div class="auth-sub">Masukkan email Anda, kami akan kirimkan link reset password</div>

@if(session('status'))
  <div class="form-status">{{ session('status') }}</div>
@endif

<form method="POST" action="{{ route('password.email') }}">
  @csrf

  <div class="form-group">
    <label class="form-label">Email</label>
    <input class="form-input" type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus>
    @error('email') <div class="form-error">{{ $message }}</div> @enderror
  </div>

  <button class="btn-primary" type="submit">Kirim Link Reset Password</button>

  <div class="auth-footer" style="justify-content:center">
    <a class="auth-link" href="{{ route('login') }}">← Kembali ke Login</a>
  </div>
</form>
@endsection
