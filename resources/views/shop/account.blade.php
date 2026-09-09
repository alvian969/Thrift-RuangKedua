@extends('layouts.app')
@section('content')
<section class="page account-page">
  <div class="admin-top">
    <div>
      <p class="eyebrow">PENGATURAN AKUN</p>
      <h1>Atur<br><em>akunmu.</em></h1>
    </div>
    <form method="POST" action="{{ route('logout') }}">@csrf<button class="button outline">Keluar</button></form>
  </div>
  <div class="account-grid">
    <form class="form-panel" method="POST" action="{{ route('account.update') }}" enctype="multipart/form-data">
      @csrf @method('PUT')
      <p class="eyebrow">DETAIL PROFIL</p>
      <h2>Data diri</h2>
      <div class="profile-editor">
        @if($user->user_profil_url && $user->user_profil_url !== 'url_placeholder_profil')
        <img src="{{ $user->user_profil_url }}" alt="Foto profil {{ $user->user_fullname }}">
        @else
        <span>{{ strtoupper(substr($user->user_fullname, 0, 1)) }}</span>
        @endif
        <label class="file-label">Foto profil<input name="user_profil" type="file" accept="image/jpeg,image/png,image/webp"><small>JPG, PNG, WEBP · maksimal 2 MB</small></label>
      </div>
      <label>Username<input value="{{ $user->user_username }}" disabled></label>
      <label>Nama lengkap<input name="user_fullname" value="{{ old('user_fullname', $user->user_fullname) }}" required></label>
      <label>Email<input name="user_email" type="email" value="{{ old('user_email', $user->user_email) }}" required></label>
      <label>No. handphone<input name="user_nohp" type="tel" value="{{ old('user_nohp', $user->user_nohp) }}" required></label>
      <label>Alamat pengiriman<input name="user_alamat" value="{{ old('user_alamat', $user->user_alamat) }}" required></label>
      <button class="button dark full">Simpan perubahan <span>→</span></button>
    </form>
    <form class="form-panel" method="POST" action="{{ route('account.password') }}">
      @csrf @method('PUT')
      <p class="eyebrow">KEAMANAN</p>
      <h2>Ganti password</h2>
      <label>Password saat ini<input name="current_password" type="password" required></label>
      <label>Password baru<input name="password" type="password" required></label>
      <label>Konfirmasi password<input name="password_confirmation" type="password" required></label>
      <button class="button outline full">Perbarui password <span>↗</span></button>
    </form>
  </div>

</section>
@endsection