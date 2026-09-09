<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? 'Ruang Kedua' }} | Ruang Kedua</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body>
  <header class="site-header"><a class="brand" href="{{ route('shop') }}"><span class="brand-mark">R2</span><span>RUANG<br><b>KEDUA</b></span></a>
    <nav><a href="{{ route('shop') }}">Katalog</a>@if(session('user_id'))<a href="{{ route('purchase.history') }}">Riwayat pembelian</a>@endif @if(session('user_level') === 'Admin')<a href="{{ route('admin.dashboard') }}">Admin</a>@endif<a href="{{ route('cart') }}">Keranjang <i>{{ array_sum(session('cart', [])) }}</i></a>@if(session('user_id'))<a class="avatar" href="{{ route('account') }}">@if(session('user_profile_url') && session('user_profile_url') !== 'url_placeholder_profil')<img src="{{ session('user_profile_url') }}" alt="Foto profil">@else{{ substr(session('user_level'), 0, 1) }}@endif</a>@else<a class="login-cta" href="{{ route('login') }}">Masuk</a>@endif</nav>
  </header>
  @if(session('success'))<div class="notice success">{{ session('success') }}</div>@endif
  @if($errors->any())<div class="notice error">{{ $errors->first() }}</div>@endif
  <main>@yield('content')</main>
  <footer><span>RUANG KEDUA / THRIFT PILIHAN</span><span>EST. 2026 · MALANG</span></footer>
</body>

</html>