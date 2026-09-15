@extends('layouts.admin')
@section('content')
<section class="page admin-page">
  <div class="admin-top">
    <div>
      <p class="eyebrow">KATEGORI / ADMIN</p>
      <h1>Kelola<br><em>kategori.</em></h1>
    </div>
    <a class="button outline" href="{{ route('admin.pakaian') }}">← Inventaris pakaian</a>
  </div>

  <div class="category-grid">
    <form class="form-panel" method="POST" action="{{ route('admin.kategori.store') }}">
      @csrf
      <p class="eyebrow">KATEGORI BARU</p>
      <h2>Tambah kategori</h2>
      <label>Nama kategori<input name="kategori_pakaian_nama" required maxlength="50" placeholder="Contoh: Jaket"></label>
      <button class="button dark full" type="submit">Simpan kategori <span>+</span></button>
    </form>

    <div class="category-list">
      <form class="admin-search" method="GET" action="{{ route('admin.kategori') }}"><input name="q" value="{{ $searchTerm }}" placeholder="Cari kategori..."><button type="submit">Cari</button>@if($searchTerm)<a class="search-clear" href="{{ route('admin.kategori') }}" aria-label="Hapus pencarian" title="Hapus pencarian">×</a>@endif</form>
      <div class="table-head"><span>Nama kategori</span><span>Jumlah produk</span><span></span></div>
      @forelse($categories as $category)
      <div class="category-row category-edit-trigger" data-target="modal-kategori-{{ $category->kategori_pakaian_id }}" tabindex="0" role="button" aria-label="Edit {{ $category->kategori_pakaian_nama }}">
        <span><b>{{ $category->kategori_pakaian_nama }}</b></span>
        <span>{{ $category->pakaian_count }} produk</span>
        <div class="row-actions">
          <form method="POST" action="{{ route('admin.kategori.destroy', $category) }}" class="delete-form">
            @csrf @method('DELETE')
            <button class="delete" type="submit" title="Hapus kategori" {{ $category->pakaian_count ? 'disabled' : '' }}>×</button>
          </form>
        </div>
      </div>
      <div class="product-modal" id="modal-kategori-{{ $category->kategori_pakaian_id }}" aria-hidden="true">
        <div class="product-modal-card">
          <button type="button" class="modal-close" aria-label="Tutup edit kategori">×</button>
          <p class="eyebrow">EDIT KATEGORI</p>
          <h3>{{ $category->kategori_pakaian_nama }}</h3>
          <form method="POST" action="{{ route('admin.kategori.update', $category) }}">
            @csrf @method('PUT')
            <label>Nama kategori<input name="kategori_pakaian_nama" value="{{ $category->kategori_pakaian_nama }}" required maxlength="50"></label>
            <button class="button dark full" type="submit">Simpan perubahan <span>→</span></button>
          </form>
        </div>
      </div>
      @empty
      <p class="empty-box">Belum ada kategori.</p>
      @endforelse
    </div>
  </div>
</section>
@endsection