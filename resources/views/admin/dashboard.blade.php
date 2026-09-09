@extends('layouts.admin')
@section('content')<section class="page admin-page">
  <div class="admin-top">
    <div>
      <p class="eyebrow">RUANG KONTROL / ADMIN</p>
      <h1>Selamat datang,<br><em>Admin.</em></h1>
    </div>
    <form method="POST" action="{{ route('logout') }}">@csrf<button class="button outline">Keluar</button></form>
  </div>
  <div class="stats">
    <div><span>Produk aktif</span><b>{{ $products }}</b><a href="{{ route('admin.pakaian') }}">Kelola →</a></div>
    <div><span>Total pembelian</span><b>{{ $orders }}</b><a href="{{ route('admin.pembelian') }}">Lihat →</a></div>
    <div><span>Pengguna terdaftar</span><b>{{ $users }}</b><a href="{{ route('admin.pengguna') }}">Lihat →</a></div>
    <div><span>Nilai transaksi</span><b>Rp {{ number_format($revenue, 0, ',', '.') }}</b><small>sepanjang waktu</small></div>
  </div>
</section>@endsection