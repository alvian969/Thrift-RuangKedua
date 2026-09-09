@extends('layouts.app')
@section('content')<section class="auth">
  <div>
    <p class="eyebrow">AKSES MEMBER</p>
    <h1>Masuk ke<br><em>ruangmu.</em></h1>
    <p>Masuk untuk menyimpan detail pesanan dan menyelesaikan checkout.</p>
  </div>
  <form class="form-panel" method="POST" action="{{ route('login.store') }}">@csrf<label>Username<input name="username" required></label><label>Password<input name="password" type="password" required></label><button class="button dark full">Masuk <span>→</span></button>
    <p class="hint">Belum punya akun? <a class="text-link" href="{{ route('register') }}">Daftar sebagai pengguna →</a></p>
  </form>
</section>@endsection