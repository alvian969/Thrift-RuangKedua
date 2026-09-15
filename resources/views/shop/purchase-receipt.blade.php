@extends('layouts.app')
@section('content')
<section class="receipt-page">
  <div class="receipt-actions">
    <a class="button outline" href="{{ route('purchase.history') }}">← Kembali ke histori</a>
    <button class="button dark" type="button" onclick="window.print()">Cetak nota</button>
  </div>

  <article class="receipt">
    <header class="receipt-header">
      <div>
        <p class="eyebrow">RUANG KEDUA</p>
        <h1>Nota<br><em>pembelian.</em></h1>
      </div>
      <div class="receipt-number">
        <span>Nomor pesanan</span>
        <b>#{{ str_pad($purchase->pembelian_id, 4, '0', STR_PAD_LEFT) }}</b>
      </div>
    </header>

    <div class="receipt-info">
      <div><span>Pembeli</span><b>{{ $purchase->user->user_fullname }}</b></div>
      <div><span>Tanggal</span><b>{{ $purchase->pembelian_tanggal->format('d M Y, H:i') }}</b></div>
      <div><span>Pembayaran</span><b>{{ $purchase->metodePembayaran?->metode_pembayaran_jenis ?? 'Tidak tersedia' }}</b></div>
      <div><span>Status</span><b>{{ str_replace('_', ' ', ucfirst($purchase->pembelian_status ?? 'diproses')) }}</b></div>
    </div>

    <div class="receipt-items">
      <div class="receipt-items-head"><span>Barang</span><span>Jumlah</span><span>Total</span></div>
      @foreach($purchase->detail as $detail)
      <div class="receipt-item">
        <span>{{ $detail->pembelian_detail_nama_pakaian ?? $detail->pakaian?->pakaian_nama ?? 'Produk tidak tersedia' }}</span>
        <span>{{ $detail->pembelian_detail_jumlah }} ×</span>
        <b>Rp {{ number_format($detail->pembelian_detail_total_harga, 0, ',', '.') }}</b>
      </div>
      @endforeach
    </div>

    <div class="receipt-total"><span>Total pembayaran</span><b>Rp {{ number_format($purchase->pembelian_total_harga, 0, ',', '.') }}</b></div>
    <p class="receipt-note">Terima kasih sudah memilih barang preloved dari Ruang Kedua.</p>
  </article>
</section>
@endsection