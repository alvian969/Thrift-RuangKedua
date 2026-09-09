@extends('layouts.app')
@section('content')<section class="page">
  <p class="eyebrow">YOUR SELECTION</p>
  <h1>Keranjang</h1>@if($products->isEmpty())<div class="empty-box">
    <p>Keranjangmu masih kosong.</p><a class="button dark" href="{{ route('shop') }}">Jelajahi katalog</a>
  </div>@else<div class="cart-layout">
    <div class="cart-list">@foreach($products as $product)<div class="cart-row"><img src="{{ $product->pakaian_gambar_url }}" alt="{{ $product->pakaian_nama }}">
        <div>
          <p class="eyebrow">{{ $product->kategori?->kategori_pakaian_nama }}</p>
          <h3>{{ $product->pakaian_nama }}</h3>
          <p>{{ $cart[$product->pakaian_id] }} × Rp {{ number_format($product->pakaian_harga, 0, ',', '.') }}</p>
        </div><strong>Rp {{ number_format($product->pakaian_harga * $cart[$product->pakaian_id], 0, ',', '.') }}</strong>
        <form class="remove-cart-form" method="POST" action="{{ route('cart.remove', $product) }}">@csrf @method('DELETE')<button class="remove-cart" type="submit" title="Hapus produk dari keranjang" aria-label="Hapus {{ $product->pakaian_nama }}">×</button></form>
      </div>@endforeach</div>
    <aside class="summary">
      <div class="summary-heading">
        <p class="eyebrow">RINGKASAN</p>
        <form method="POST" action="{{ route('cart.clear') }}">@csrf @method('DELETE')<button class="clear-cart" type="submit">Kosongkan semua</button></form>
      </div>
      <div><span>Total item</span><b>{{ array_sum($cart) }}</b></div>
      <div class="total"><span>Total</span><b>Rp {{ number_format($products->sum(fn($p) => $p->pakaian_harga * $cart[$p->pakaian_id]), 0, ',', '.') }}</b></div>@if(!session('user_id'))<a class="button dark full" href="{{ route('login') }}">Login untuk checkout <span>→</span></a><small>Login atau daftar untuk memilih metode pembayaran.</small>@else<form method="POST" action="{{ route('checkout') }}">@csrf<label class="payment-label">Pilih metode pembayaran</label>@if($paymentMethods->isNotEmpty())<div class="payment-options">@foreach($paymentMethods as $method)<label class="payment-option"><input type="radio" name="metode_pembayaran_id" value="{{ $method->metode_pembayaran_id }}" required><span>@php $paymentLabel = $method->metode_pembayaran_nomor ? $method->metode_pembayaran_jenis : 'COD'; $paymentDetail = $method->metode_pembayaran_nomor ?: 'Bayar di tempat'; @endphp<b>{{ $paymentLabel }}</b><small>{{ $paymentDetail }}</small></span></label>@endforeach</div>@else<p class="empty">Belum ada metode tersimpan.</p>@endif<button class="button dark full">Checkout sekarang <span>→</span></button></form>
      <details class="add-payment">
        <summary>Tambah metode pembayaran saat checkout</summary>
        <form method="POST" action="{{ route('payment.store') }}">@csrf<label>Jenis<select name="metode_pembayaran_jenis" required>
              <option value="DANA">DANA</option>
              <option value="OVO">OVO</option>
              <option value="BCA">BCA</option>
              <option value="COD">COD</option>
            </select></label><label>Nomor rekening / akun<input name="metode_pembayaran_nomor" placeholder="Kosongkan untuk COD"></label><button class="button outline full">Simpan metode <span>+</span></button></form>
      </details><small>Pilih metode pembayaran sebelum menyelesaikan pesanan.</small>@endif
    </aside>
  </div>@endif
</section>@endsection