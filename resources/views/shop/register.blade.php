@extends('layouts.app')
@section('content')
<section class="auth register-page">
  <div>
    <p class="eyebrow">MEMBER BARU</p>
    <h1>Buat<br><em>ruangmu.</em></h1>
    <p>Daftar sebagai pengguna untuk berbelanja dan mengelola pesananmu di Ruang Kedua.</p>
  </div>
  <form class="form-panel" method="POST" action="{{ route('register.store') }}">
    @csrf
    <label>Username<input name="user_username" value="{{ old('user_username') }}" required></label>
    <label>Nama lengkap<input name="user_fullname" value="{{ old('user_fullname') }}" required></label>
    <label>Email<input name="user_email" type="email" value="{{ old('user_email') }}" required></label>
    <label>No. handphone<input name="user_nohp" type="tel" value="{{ old('user_nohp') }}" required></label>
    <label>Alamat pengiriman<input name="user_alamat" value="{{ old('user_alamat') }}" required></label>
    <label>Password<input name="password" type="password" required></label>
    <label>Konfirmasi password<input name="password_confirmation" type="password" required></label>
    <button class="button dark full">Buat akun <span>→</span></button>
    <p class="hint">Sudah punya akun? <a class="text-link" href="{{ route('login') }}">Masuk di sini →</a></p>
  </form>
</section>
@endsection