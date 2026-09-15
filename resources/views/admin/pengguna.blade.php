@extends('layouts.admin')
@section('content')
<section class="page admin-page">
  <div class="admin-top">
    <div>
      <p class="eyebrow">PENGGUNA / ADMIN</p>
      <h1>Data<br><em>pengguna.</em></h1>
    </div><a class="button outline" href="{{ route('admin.dashboard') }}">← Dashboard</a>
  </div>
  <div class="user-list">
    <form class="admin-search" method="GET" action="{{ route('admin.pengguna') }}"><input name="q" value="{{ $searchTerm }}" placeholder="Cari nama, pengguna, atau email..."><button type="submit">Cari</button>@if($searchTerm)<a class="search-clear" href="{{ route('admin.pengguna') }}" aria-label="Hapus pencarian" title="Hapus pencarian">×</a>@endif</form>
    <div class="user-list-head"><span>Pengguna</span><span>Kontak</span><span>Role</span><span>Status</span></div>
    @forelse($users as $user)
    <article class="user-row">
      <div class="user-identity">
        @if($user->user_profil_url && $user->user_profil_url !== 'url_placeholder_profil')<img src="{{ $user->user_profil_url }}" alt="Foto {{ $user->user_fullname }}">@else<span>{{ strtoupper(substr($user->user_fullname, 0, 1)) }}</span>@endif
        <div><b>{{ $user->user_fullname }}</b><small>{{ '@' . $user->user_username }}</small></div>
      </div>
      <div><b>{{ $user->user_email }}</b><small>{{ $user->user_nohp }}</small></div>
      <span class="role {{ strtolower($user->user_level) }}">{{ $user->user_level }}</span>
      <span class="account-status"><i></i> Terdaftar</span>
    </article>
    @empty
    <p class="empty-box">Belum ada pengguna terdaftar.</p>
    @endforelse
  </div>
</section>
@endsection