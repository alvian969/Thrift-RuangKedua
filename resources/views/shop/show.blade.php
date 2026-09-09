@extends('layouts.app')
@section('content')<section class="detail"><img src="{{ $pakaian->pakaian_gambar_url }}" alt="{{ $pakaian->pakaian_nama }}">
  <div>
    <p class="eyebrow">{{ $pakaian->kategori?->kategori_pakaian_nama }} / ONE OF ONE</p>
    <h1>{{ $pakaian->pakaian_nama }}</h1><strong class="detail-price">Rp {{ number_format($pakaian->pakaian_harga, 0, ',', '.') }}</strong>
    <p class="detail-copy">Satu item unik yang siap menemukan pemilik berikutnya. Diperiksa dan dikurasi oleh tim Ruang Kedua.</p>
    <form method="POST" action="{{ route('cart.add', $pakaian) }}">@csrf<button class="button dark">Tambah ke keranjang <span>+</span></button></form><a class="back" href="{{ route('shop') }}">← Kembali ke katalog</a>
  </div>
</section>@endsection