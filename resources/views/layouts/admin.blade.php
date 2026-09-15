<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? 'Admin' }} | Ruang Kedua</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="admin-shell">
  <aside class="admin-sidebar">
    <a class="brand" href="{{ route('admin.dashboard') }}"><span class="brand-mark">R2</span><span>RUANG<br><b>ADMIN</b></span></a>
    <p class="eyebrow">RUANG KERJA</p>
    <nav class="admin-nav">
      <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><span>01</span>Dashboard</a>
      <a class="{{ request()->routeIs('admin.pakaian*') ? 'active' : '' }}" href="{{ route('admin.pakaian') }}"><span>02</span>Inventaris pakaian</a>
      <a class="{{ request()->routeIs('admin.kategori*') ? 'active' : '' }}" href="{{ route('admin.kategori') }}"><span>03</span>Kelola kategori</a>
      <a class="{{ request()->routeIs('admin.pembelian') ? 'active' : '' }}" href="{{ route('admin.pembelian') }}"><span>04</span>Data pembelian</a>
      <a class="{{ request()->routeIs('admin.pengguna') ? 'active' : '' }}" href="{{ route('admin.pengguna') }}"><span>05</span>Data pengguna</a>
    </nav>
    <div class="admin-sidebar-bottom"><a href="{{ route('shop') }}">← Lihat toko</a>
      <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Keluar <span>↗</span></button></form>
    </div>
  </aside>
  <div class="admin-content">
    <header class="admin-header"><span>RUANG KONTROL</span><span>ADMIN / {{ now()->format('d.m.Y') }}</span></header>
    @if(session('success'))<div class="notice success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="notice error">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="notice error">{{ $errors->first() }}</div>@endif
    <main class="admin-main">@yield('content')</main>
    <footer><span>RUANG KEDUA / RUANG KERJA ADMIN</span><span>EST. 2026 · MALANG</span></footer>
  </div>
</body>

</html>