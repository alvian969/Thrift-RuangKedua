@extends('layouts.admin')
@section('content')<section class="page admin-page">
  <div class="admin-top">
    <div>
      <p class="eyebrow">PEMBELIAN / ADMIN</p>
      <h1>Data<br><em>pembelian.</em></h1>
    </div><a class="button outline" href="{{ route('admin.dashboard') }}">← Dashboard</a>
  </div>
  <form class="order-filter" method="GET" action="{{ route('admin.pembelian') }}"><label>Filter status<select name="status" onchange="this.form.submit()">
        <option value="">Semua status</option>
        <option value="diproses" {{ $selectedStatus === 'diproses' ? 'selected' : '' }}>Diproses</option>
        <option value="menunggu_pembatalan" {{ $selectedStatus === 'menunggu_pembatalan' ? 'selected' : '' }}>Menunggu pembatalan</option>
        <option value="dikirim" {{ $selectedStatus === 'dikirim' ? 'selected' : '' }}>Dikirim</option>
        <option value="selesai" {{ $selectedStatus === 'selesai' ? 'selected' : '' }}>Selesai</option>
        <option value="dibatalkan" {{ $selectedStatus === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
      </select></label></form>
  <div class="orders">@forelse($orders as $order)<article class="order">
      <div class="order-head"><span>#{{ str_pad($order->pembelian_id, 4, '0', STR_PAD_LEFT) }} · {{ $order->user->user_fullname }}</span><time>{{ $order->pembelian_tanggal->format('d M Y, H:i') }}</time><b>Rp {{ number_format($order->pembelian_total_harga, 0, ',', '.') }}</b></div>
      <div class="order-meta"><small>Status: {{ str_replace('_', ' ', ucfirst($order->pembelian_status ?? 'diproses')) }}</small></div>
      <div class="order-items">@foreach($order->detail as $detail)<span>{{ $detail->pakaian->pakaian_nama }} <small>× {{ $detail->pembelian_detail_jumlah }}</small></span>@endforeach</div>
      <div class="order-actions">
        @if($order->pembelian_status === 'menunggu_pembatalan')
        <form method="POST" action="{{ route('admin.pembelian.pembatalan', $order) }}">@csrf<input type="hidden" name="keputusan" value="setujui"><button class="button dark" type="submit">Setujui pembatalan</button></form>
        <form method="POST" action="{{ route('admin.pembelian.pembatalan', $order) }}">@csrf<input type="hidden" name="keputusan" value="tolak"><button class="button outline" type="submit">Tolak pembatalan</button></form>
        @else
        <form method="POST" action="{{ route('admin.pembelian.status', $order) }}">@csrf @method('PUT')<label>Status<select name="pembelian_status" onchange="this.form.submit()">
              <option value="diproses" {{ ($order->pembelian_status ?? 'diproses') === 'diproses' ? 'selected' : '' }}>Diproses</option>
              <option value="dikirim" {{ $order->pembelian_status === 'dikirim' ? 'selected' : '' }}>Dikirim</option>
              <option value="selesai" {{ $order->pembelian_status === 'selesai' ? 'selected' : '' }}>Selesai</option>
              <option value="dibatalkan" {{ $order->pembelian_status === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
            </select></label></form>
        @endif
      </div>
    </article>@empty<p class="empty-box">Belum ada pembelian.</p>@endforelse</div>
</section>@endsection