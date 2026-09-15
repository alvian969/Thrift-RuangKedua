@extends('layouts.admin')
@section('content')<section class="page admin-page">
  <div class="admin-top">
    <div>
      <p class="eyebrow">INVENTORY / ADMIN</p>
      <h1>Manajemen<br><em>pakaian.</em></h1>
    </div><a class="button outline" href="{{ route('admin.dashboard') }}">← Dashboard</a>
  </div>
  <div class="admin-grid">
    <form class="form-panel" method="POST" action="{{ route('admin.pakaian.store') }}">@csrf<h2>Tambah item</h2><label>Nama pakaian<input name="pakaian_nama" required></label><label>Kategori<select name="pakaian_kategori_pakaian_id" required>@foreach($categories as $category)<option value="{{ $category->kategori_pakaian_id }}">{{ $category->kategori_pakaian_nama }}</option>@endforeach</select></label><label>Harga<input name="pakaian_harga" type="number" required></label><label>Stok<input name="pakaian_stok" type="number" required></label><label>URL gambar<input name="pakaian_gambar_url" type="url" required></label><button class="button dark full">Simpan item <span>+</span></button></form>
    <div class="inventory">
      <form class="admin-search" method="GET" action="{{ route('admin.pakaian') }}"><input name="q" value="{{ $searchTerm }}" placeholder="Cari nama pakaian..."><button type="submit">Cari</button>@if($searchTerm || $selectedCategory)<a class="search-clear" href="{{ $selectedCategory ? route('admin.pakaian', ['kategori' => $selectedCategory]) : route('admin.pakaian') }}" aria-label="Hapus pencarian" title="Hapus pencarian">×</a>@endif</form>
      <form class="inventory-filter" method="GET" action="{{ route('admin.pakaian') }}"><input type="hidden" name="q" value="{{ $searchTerm }}"><label>Filter kategori<select name="kategori" onchange="this.form.submit()">
            <option value="">Semua kategori</option>@foreach($categories as $category)<option value="{{ $category->kategori_pakaian_id }}" {{ (string) $selectedCategory === (string) $category->kategori_pakaian_id ? 'selected' : '' }}>{{ $category->kategori_pakaian_nama }}</option>@endforeach
          </select></label></form>
      <div class="table-head"><span>Item</span><span>Harga</span><span>Stok</span><span></span></div>@foreach($products as $product)<div class="table-row product-edit-trigger" data-target="modal-{{ $product->pakaian_id }}" tabindex="0" role="button" aria-label="Edit {{ $product->pakaian_nama }}"><span><b>{{ $product->pakaian_nama }}</b><small>{{ $product->kategori->kategori_pakaian_nama }}</small></span><span>Rp {{ number_format($product->pakaian_harga, 0, ',', '.') }}</span><span>{{ $product->pakaian_stok }}</span>
        <div class="row-actions">
          <form method="POST" action="{{ route('admin.pakaian.destroy', $product) }}" class="delete-form">@csrf @method('DELETE')<button class="delete" title="Hapus item" type="submit">×</button></form>
        </div>
      </div>
      <div class="product-modal" id="modal-{{ $product->pakaian_id }}" aria-hidden="true">
        <div class="product-modal-card">
          <button type="button" class="modal-close" aria-label="Tutup edit produk">×</button>
          <p class="eyebrow">EDIT PRODUK</p>
          <h3>{{ $product->pakaian_nama }}</h3>
          <form method="POST" action="{{ route('admin.pakaian.update', $product) }}">@csrf @method('PUT')
            <label>Nama<input name="pakaian_nama" value="{{ $product->pakaian_nama }}" required></label>
            <label>Kategori<select name="pakaian_kategori_pakaian_id" required>@foreach($categories as $category)<option value="{{ $category->kategori_pakaian_id }}" {{ $product->pakaian_kategori_pakaian_id == $category->kategori_pakaian_id ? 'selected' : '' }}>{{ $category->kategori_pakaian_nama }}</option>@endforeach</select></label>
            <label>Harga<input name="pakaian_harga" type="number" value="{{ $product->pakaian_harga }}" required></label>
            <label>Stok<input name="pakaian_stok" type="number" value="{{ $product->pakaian_stok }}" required></label>
            <label>URL gambar<input name="pakaian_gambar_url" type="url" value="{{ $product->pakaian_gambar_url }}" required></label>
            <button class="button dark full">Simpan perubahan <span>→</span></button>
          </form>
        </div>
      </div>@endforeach
      @if($products->hasPages())
      <div class="pagination-wrap">{{ $products->links() }}</div>
      @endif
    </div>
  </div>
</section>@endsection