@extends('layouts.app')
@section('content')
<section class="hero">
  <div>
    <p class="eyebrow">THRIFT PILIHAN / 001</p>
    <h1>Pakaian lama,<br><em>cerita baru.</em></h1>
    <p class="hero-copy">Pilihan pakaian preloved yang dikurasi untuk menemani bab berikutnya dalam hidupmu.</p><a class="button dark" href="#koleksi">Lihat koleksi <span>↓</span></a>
  </div>
  <div class="hero-art"><span>THRIFT<br>ADALAH<br>PILIHAN</span><strong>R2</strong></div>
</section>
<section class="collection" id="koleksi">
  <div class="section-head">
    <div>
      <p class="eyebrow">KOLEKSI</p>
      <h2>Temuan minggu ini</h2>
    </div>
    <form class="search"><input name="q" value="{{ request('q') }}" placeholder="Cari item..." aria-label="Cari item"><button>↗</button></form>
  </div>
  <div class="filters"><a class="{{ !request('kategori') ? 'active' : '' }}" href="{{ route('shop') }}">Semua item</a>@foreach($categories as $category)<a class="{{ request('kategori') == $category->kategori_pakaian_id ? 'active' : '' }}" href="?kategori={{ $category->kategori_pakaian_id }}">{{ $category->kategori_pakaian_nama }}</a>@endforeach</div>
  <div class="product-grid">@forelse($products as $product)<article class="product"><a class="product-image" href="{{ route('product.show', $product) }}"><img src="{{ $product->pakaian_gambar_url }}" alt="{{ $product->pakaian_nama }}"><span class="tag">{{ $product->kategori->kategori_pakaian_nama }}</span></a>
      <div class="product-info">
        <div>
          <h3>{{ $product->pakaian_nama }}</h3>
          <p>{{ $product->pakaian_stok }} tersedia</p>
        </div><strong>Rp {{ number_format($product->pakaian_harga, 0, ',', '.') }}</strong>
      </div>
      <form method="POST" action="{{ route('cart.add', $product) }}">@csrf<button class="add">Tambah ke keranjang <span>+</span></button></form>
    </article>@empty<p class="empty">Belum ada temuan yang cocok.</p>@endforelse</div>
  @if($products->hasPages())
  <div class="pagination-wrap">{{ $products->links() }}</div>
  @endif
</section>
<section class="manifest">
  <p class="eyebrow">MOTO KAMI</p>
  <p>“Barang terbaik bukan yang paling baru. Tapi yang masih punya banyak kemungkinan.”</p>
</section>
@endsection