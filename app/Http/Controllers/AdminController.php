<?php

namespace App\Http\Controllers;

use App\Models\KategoriPakaian;
use App\Models\Pakaian;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
  private function guard(): void
  {
    abort_unless(session('user_level') === 'Admin', 403);
  }
  public function index()
  {
    $this->guard();
    return view('admin.dashboard', [
      'products' => Pakaian::count(),
      'orders' => Pembelian::count(),
      'revenue' => Pembelian::sum('pembelian_total_harga'),
      'users' => User::where('user_level', 'Pengguna')->count(),
    ]);
  }
  public function pakaian(Request $request)
  {
    $this->guard();
    $query = Pakaian::with('kategori')->latest('pakaian_id');
    if ($request->filled('kategori')) {
      $query->where('pakaian_kategori_pakaian_id', $request->kategori);
    }
    if ($request->filled('q')) {
      $query->where('pakaian_nama', 'like', '%' . $request->q . '%');
    }

    return view('admin.pakaian', [
      'products' => $query->paginate(6)->appends($request->only(['kategori', 'q'])),
      'categories' => KategoriPakaian::all(),
      'selectedCategory' => $request->kategori,
      'searchTerm' => $request->q,
    ]);
  }
  public function storePakaian(Request $request)
  {
    $this->guard();
    $data = $request->validate(['pakaian_kategori_pakaian_id' => 'required|exists:kategori_pakaian,kategori_pakaian_id', 'pakaian_nama' => 'required|max:50', 'pakaian_harga' => 'required|integer|min:0', 'pakaian_stok' => 'required|integer|min:0', 'pakaian_gambar_url' => 'required|url|max:255']);
    Pakaian::create($data);
    return back()->with('success', 'Produk berhasil ditambahkan.');
  }
  public function kategori(Request $request)
  {
    $this->guard();
    $query = KategoriPakaian::withCount('pakaian')->orderBy('kategori_pakaian_nama');
    if ($request->filled('q')) {
      $query->where('kategori_pakaian_nama', 'like', '%' . $request->q . '%');
    }
    return view('admin.kategori', [
      'categories' => $query->get(),
      'searchTerm' => $request->q,
    ]);
  }

  public function storeKategori(Request $request)
  {
    $this->guard();
    $data = $request->validate([
      'kategori_pakaian_nama' => 'required|string|max:50|unique:kategori_pakaian,kategori_pakaian_nama',
    ]);
    KategoriPakaian::create($data);
    return back()->with('success', 'Kategori berhasil ditambahkan.');
  }

  public function updateKategori(Request $request, KategoriPakaian $kategoriPakaian)
  {
    $this->guard();
    $data = $request->validate([
      'kategori_pakaian_nama' => 'required|string|max:50|unique:kategori_pakaian,kategori_pakaian_nama,' . $kategoriPakaian->kategori_pakaian_id . ',kategori_pakaian_id',
    ]);
    $kategoriPakaian->update($data);
    return back()->with('success', 'Kategori berhasil diperbarui.');
  }

  public function destroyKategori(KategoriPakaian $kategoriPakaian)
  {
    $this->guard();
    abort_if($kategoriPakaian->pakaian()->exists(), 422, 'Kategori yang masih memiliki produk tidak dapat dihapus.');
    $kategoriPakaian->delete();
    return back()->with('success', 'Kategori berhasil dihapus.');
  }

  public function updatePakaian(Request $request, Pakaian $pakaian)
  {
    $this->guard();
    $data = $request->validate([
      'pakaian_kategori_pakaian_id' => 'required|exists:kategori_pakaian,kategori_pakaian_id',
      'pakaian_nama' => 'required|max:50',
      'pakaian_harga' => 'required|integer|min:0',
      'pakaian_stok' => 'required|integer|min:0',
      'pakaian_gambar_url' => 'required|url|max:255',
    ]);

    $pakaian->update($data);

    return back()->with('success', 'Produk berhasil diperbarui.');
  }
  public function destroyPakaian(Pakaian $pakaian)
  {
    $this->guard();

    $hasActivePurchase = PembelianDetail::where('pembelian_detail_pakaian_id', $pakaian->pakaian_id)
      ->whereHas('pembelian', function ($query) {
        $query->whereIn('pembelian_status', ['diproses', 'menunggu_pembatalan', 'dikirim']);
      })
      ->exists();

    if ($hasActivePurchase) {
      return back()->with('error', 'Barang yang masih dalam proses pembelian tidak dapat dihapus.');
    }

    $pakaian->delete();
    return back()->with('success', 'Produk dihapus.');
  }
  public function pembelian(Request $request)
  {
    $this->guard();
    $query = Pembelian::with(['user', 'detail.pakaian'])->latest('pembelian_id');
    if ($request->filled('status')) {
      $query->where('pembelian_status', $request->status);
    }
    if ($request->filled('q')) {
      $query->where(function ($nested) use ($request) {
        $nested->where('pembelian_id', $request->q)
          ->orWhereHas('user', function ($userQuery) use ($request) {
            $userQuery->where('user_fullname', 'like', '%' . $request->q . '%')
              ->orWhere('user_username', 'like', '%' . $request->q . '%');
          })
          ->orWhereHas('detail.pakaian', function ($productQuery) use ($request) {
            $productQuery->where('pakaian_nama', 'like', '%' . $request->q . '%');
          });
      });
    }

    return view('admin.pembelian', [
      'orders' => $query->get(),
      'selectedStatus' => $request->status,
      'searchTerm' => $request->q,
    ]);
  }

  public function updateStatus(Request $request, Pembelian $pembelian)
  {
    $this->guard();
    $data = $request->validate([
      'pembelian_status' => 'required|in:diproses,dikirim,selesai,dibatalkan',
    ]);

    abort_if(
      $pembelian->pembelian_status === 'dibatalkan' && $data['pembelian_status'] !== 'dibatalkan',
      422,
      'Pesanan yang sudah dibatalkan tidak dapat diaktifkan kembali.'
    );

    if ($data['pembelian_status'] === 'dibatalkan' && $pembelian->pembelian_status !== 'dibatalkan') {
      foreach ($pembelian->detail()->with('pakaian')->get() as $detail) {
        $detail->pakaian?->increment('pakaian_stok', $detail->pembelian_detail_jumlah);
      }
    }

    $pembelian->update($data);

    return back()->with('success', 'Status pembelian berhasil diperbarui.');
  }

  public function handleCancellation(Request $request, Pembelian $pembelian)
  {
    $this->guard();
    $data = $request->validate([
      'keputusan' => 'required|in:setujui,tolak',
    ]);

    abort_if($pembelian->pembelian_status !== 'menunggu_pembatalan', 422, 'Pembelian ini tidak sedang menunggu pembatalan.');

    if ($data['keputusan'] === 'setujui') {
      foreach ($pembelian->detail()->with('pakaian')->get() as $detail) {
        $detail->pakaian?->increment('pakaian_stok', $detail->pembelian_detail_jumlah);
      }
      $pembelian->update(['pembelian_status' => 'dibatalkan']);
      return back()->with('success', 'Pembatalan pembelian disetujui dan stok dikembalikan.');
    }

    $pembelian->update(['pembelian_status' => 'diproses']);
    return back()->with('success', 'Pembatalan pembelian ditolak. Pesanan kembali diproses.');
  }

  public function pengguna(Request $request)
  {
    $this->guard();
    $query = User::orderBy('user_level')->orderBy('user_fullname');
    if ($request->filled('q')) {
      $query->where(function ($nested) use ($request) {
        $nested->where('user_fullname', 'like', '%' . $request->q . '%')
          ->orWhere('user_username', 'like', '%' . $request->q . '%')
          ->orWhere('user_email', 'like', '%' . $request->q . '%');
      });
    }
    return view('admin.pengguna', [
      'users' => $query->get(),
      'searchTerm' => $request->q,
    ]);
  }
}
