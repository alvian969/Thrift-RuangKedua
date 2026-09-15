@extends('layouts.app')
@section('content')
<section class="page">
  <div class="admin-top">
    <div>
      <p class="eyebrow">RIWAYAT PESANAN</p>
      <h1>Histori<br><em>pembelian.</em></h1>
    </div>

  </div>

  @if($purchases->isEmpty())
  <p class="empty-box">Belum ada pembelian yang dilakukan.</p>
  @else
  <div class="orders">
    @foreach($purchases as $purchase)
    <article class="order receipt-card" data-receipt-url="{{ route('purchase.receipt', $purchase) }}" tabindex="0" role="link" aria-label="Buka nota pesanan #{{ str_pad($purchase->pembelian_id, 4, '0', STR_PAD_LEFT) }}">
      <div class="order-head">
        <span>#{{ str_pad($purchase->pembelian_id, 4, '0', STR_PAD_LEFT) }}</span>
        <time>{{ $purchase->pembelian_tanggal->format('d M Y, H:i') }}</time>
        <b>Rp {{ number_format($purchase->pembelian_total_harga, 0, ',', '.') }}</b>
      </div>
      <div class="order-meta">
        <small>Metode pembayaran: {{ $purchase->metodePembayaran?->metode_pembayaran_jenis ?? 'Tidak tersedia' }}</small>
        <small>Status: {{ str_replace('_', ' ', ucfirst($purchase->pembelian_status ?? 'diproses')) }}</small>
      </div>
      <div class="order-items">
        @foreach($purchase->detail as $detail)
        <span>{{ $detail->pembelian_detail_nama_pakaian ?? $detail->pakaian?->pakaian_nama ?? 'Produk tidak tersedia' }} <small>× {{ $detail->pembelian_detail_jumlah }}</small></span>
        @endforeach
      </div>
      @if(($purchase->pembelian_status ?? 'diproses') === 'diproses')
      <form method="POST" action="{{ route('purchase.cancel', $purchase) }}" class="cancel-purchase-form">
        @csrf
        <button class="button outline" type="submit">Ajukan pembatalan</button>
      </form>
      @endif
    </article>
    @endforeach
  </div>
  @endif
</section>
@endsection