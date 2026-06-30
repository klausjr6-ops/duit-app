@extends('layouts.duit')
@section('title', 'Profil')

@push('styles')
<style>
  .page-header{margin-bottom:24px;}
  .page-title{font-size:22px;font-weight:700;}
  .page-sub{font-size:13px;color:var(--text-muted);margin-top:4px;}
  .card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);padding:24px;margin-bottom:20px;}
  .card-title{font-size:15px;font-weight:700;margin-bottom:4px;}
  .card-sub{font-size:12px;color:var(--text-muted);margin-bottom:20px;}
  .form-group{margin-bottom:16px;}
  .form-label{font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;display:block;}
  .form-input{width:100%;max-width:420px;background:var(--bg-input);border:1px solid var(--border);border-radius:var(--radius-sm);padding:10px 14px;color:var(--text-main);font-size:14px;outline:none;font-family:inherit;transition:border-color .2s;}
  .form-input:focus{border-color:var(--teal);}
  .btn-primary{background:var(--teal);color:#000;border:none;border-radius:var(--radius-sm);padding:10px 22px;font-weight:700;font-size:13px;cursor:pointer;}
  .btn-primary:hover{opacity:.9;}
  .btn-danger-outline{background:transparent;color:var(--red);border:1px solid var(--red);border-radius:var(--radius-sm);padding:10px 22px;font-weight:700;font-size:13px;cursor:pointer;}
  .btn-danger{background:var(--red);color:#fff;border:none;border-radius:var(--radius-sm);padding:10px 22px;font-weight:700;font-size:13px;cursor:pointer;}
  .btn-logout{background:var(--bg-input);color:var(--text-main);border:1px solid var(--border);border-radius:var(--radius-sm);padding:10px 22px;font-weight:700;font-size:13px;cursor:pointer;display:flex;align-items:center;gap:8px;}
  .btn-logout:hover{border-color:var(--red);color:var(--red);}
  .alert-success{background:rgba(0,212,170,.1);border:1px solid rgba(0,212,170,.3);color:var(--teal);padding:12px 16px;border-radius:var(--radius-sm);margin-bottom:16px;font-size:13px;}
  .alert-error{background:rgba(255,91,91,.1);border:1px solid rgba(255,91,91,.3);color:var(--red);padding:8px 12px;border-radius:var(--radius-sm);margin-top:6px;font-size:12px;}
  .danger-zone{border-color:rgba(255,91,91,.3);}

  /* Avatar */
  .avatar-row{display:flex;align-items:center;gap:20px;margin-bottom:20px;}
  .avatar-preview{width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,var(--teal),var(--blue));display:flex;align-items:center;justify-content:center;font-size:32px;font-weight:700;color:#000;overflow:hidden;flex-shrink:0;}
  .avatar-preview img{width:100%;height:100%;object-fit:cover;}
  .avatar-upload-btn{background:var(--bg-input);border:1px solid var(--border);border-radius:var(--radius-sm);padding:8px 16px;font-size:12px;color:var(--text-main);cursor:pointer;display:inline-block;}
  .avatar-upload-btn:hover{border-color:var(--teal);}
  .danger-row{display:flex;justify-content:space-between;align-items:center;}
  .danger-text{font-size:13px;color:var(--text-muted);max-width:320px;}
</style>
@endpush

@section('content')
<div class="page-header">
  <div class="page-title">⚙️ Profil & Akun</div>
  <div class="page-sub">Kelola informasi akun dan keamanan Anda</div>
</div>

@if(session('status') === 'profile-updated')
  <div class="alert-success">✅ Profil berhasil diperbarui!</div>
@endif
@if(session('status') === 'password-updated')
  <div class="alert-success">✅ Password berhasil diubah!</div>
@endif

<!-- FOTO PROFIL -->
<div class="card">
  <div class="card-title">Foto Profil</div>
  <div class="card-sub">Foto ini akan ditampilkan di sidebar aplikasi</div>

  <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
    @csrf
    @method('patch')
    <input type="hidden" name="name" value="{{ auth()->user()->name }}">
    <input type="hidden" name="email" value="{{ auth()->user()->email }}">

    <div class="avatar-row">
      <div class="avatar-preview" id="avatar-preview">
        @if(auth()->user()->avatar ?? false)
          <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar">
        @else
          {{ substr(auth()->user()->name, 0, 1) }}
        @endif
      </div>
      <div>
        <label class="avatar-upload-btn" for="avatar-input">📷 Pilih Foto Baru</label>
        <input type="file" id="avatar-input" name="avatar" accept="image/*" style="display:none" onchange="previewAvatar(this)">
        <div style="font-size:11px;color:var(--text-dim);margin-top:8px">JPG, PNG, maks 2MB</div>
      </div>
    </div>
    <button class="btn-primary" type="submit">Simpan Foto</button>
  </form>
</div>

<!-- INFO PROFIL -->
<div class="card">
  <div class="card-title">Informasi Profil</div>
  <div class="card-sub">Update nama dan alamat email Anda</div>

  <form method="POST" action="{{ route('profile.update') }}">
    @csrf
    @method('patch')

    <div class="form-group">
      <label class="form-label">Nama Lengkap</label>
      <input class="form-input" type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required>
      @error('name') <div class="alert-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
      <label class="form-label">Alamat Email</label>
      <input class="form-input" type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
      @error('email') <div class="alert-error">{{ $message }}</div> @enderror

      @if(auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
        <div style="font-size:12px;color:var(--amber);margin-top:8px">
          Email Anda belum terverifikasi.
          <button form="send-verification" type="submit" style="background:none;border:none;color:var(--teal);text-decoration:underline;cursor:pointer;font-size:12px;">Kirim ulang verifikasi</button>
        </div>
      @endif
    </div>

    <button class="btn-primary" type="submit">Simpan Perubahan</button>
  </form>

  @if(auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
    <form id="send-verification" method="POST" action="{{ route('verification.send') }}"></form>
  @endif
</div>

<!-- GANTI PASSWORD -->
<div class="card">
  <div class="card-title">Ubah Password</div>
  <div class="card-sub">Gunakan password yang kuat dan belum pernah dipakai sebelumnya</div>

  <form method="POST" action="{{ route('password.update') }}">
    @csrf
    @method('put')

    <div class="form-group">
      <label class="form-label">Password Saat Ini</label>
      <input class="form-input" type="password" name="current_password" required>
      @error('current_password', 'updatePassword') <div class="alert-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
      <label class="form-label">Password Baru</label>
      <input class="form-input" type="password" name="password" required>
      @error('password', 'updatePassword') <div class="alert-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
      <label class="form-label">Konfirmasi Password Baru</label>
      <input class="form-input" type="password" name="password_confirmation" required>
    </div>

    <button class="btn-primary" type="submit">Update Password</button>
  </form>
</div>

<!-- LOGOUT -->
<div class="card">
  <div class="card-title">Sesi Login</div>
  <div class="card-sub">Keluar dari akun Anda di perangkat ini</div>
  <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button class="btn-logout" type="submit">🚪 Sign Out</button>
  </form>
</div>

<!-- HAPUS AKUN -->
<div class="card danger-zone">
  <div class="danger-row">
    <div>
      <div class="card-title" style="color:var(--red)">Hapus Akun</div>
      <div class="danger-text">Setelah akun dihapus, semua data Anda akan dihapus permanen. Unduh data penting sebelum menghapus akun.</div>
    </div>
    <button class="btn-danger-outline" onclick="document.getElementById('modal-delete').classList.add('open')">Hapus Akun</button>
  </div>
</div>

<!-- Modal Hapus Akun -->
<div class="modal-overlay" id="modal-delete" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:300;align-items:center;justify-content:center;" onclick="if(event.target===this)this.style.display='none'">
  <div style="width:100%;max-width:420px;background:var(--bg-card);border-radius:var(--radius-lg);border:1px solid var(--border);padding:28px;">
    <div style="font-size:17px;font-weight:700;margin-bottom:8px;color:var(--red)">⚠️ Hapus Akun Permanen?</div>
    <div style="font-size:13px;color:var(--text-muted);margin-bottom:20px;">Tindakan ini tidak bisa dibatalkan. Semua transaksi, jadwal, dan goals Anda akan terhapus.</div>
    <form method="POST" action="{{ route('profile.destroy') }}">
      @csrf
      @method('delete')
      <div class="form-group">
        <label class="form-label">Masukkan Password untuk Konfirmasi</label>
        <input class="form-input" type="password" name="password" required style="max-width:100%">
      </div>
      <div style="display:flex;gap:10px;margin-top:16px">
        <button type="button" class="btn-logout" style="flex:1;justify-content:center" onclick="document.getElementById('modal-delete').style.display='none'">Batal</button>
        <button type="submit" class="btn-danger" style="flex:1;justify-content:center">Ya, Hapus Akun</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
function previewAvatar(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById('avatar-preview').innerHTML = `<img src="${e.target.result}" alt="Avatar">`;
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>
@endpush
