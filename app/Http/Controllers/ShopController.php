<?php

namespace App\Http\Controllers;

use App\Models\MetodePembayaran;
use App\Models\Pakaian;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ShopController extends Controller
{
  public function index(Request $request)
  {
    $query = Pakaian::with('kategori')->where('pakaian_stok', '>', 0);
    if ($request->filled('kategori')) $query->where('pakaian_kategori_pakaian_id', $request->kategori);
    if ($request->filled('q')) $query->where('pakaian_nama', 'like', '%' . $request->q . '%');

    $products = $query->latest('pakaian_id')->paginate(5)->appends($request->only(['kategori', 'q']));

    return view('shop.index', ['products' => $products, 'categories' => \App\Models\KategoriPakaian::all()]);
  }

  public function show(Pakaian $pakaian)
  {
    return view('shop.show', compact('pakaian'));
  }

  public function add(Request $request, Pakaian $pakaian)
  {
    $qty = min(max((int) $request->input('jumlah', 1), 1), $pakaian->pakaian_stok);
    $cart = session('cart', []);
    $cart[$pakaian->pakaian_id] = min(($cart[$pakaian->pakaian_id] ?? 0) + $qty, $pakaian->pakaian_stok);
    session(['cart' => $cart]);
    return back()->with('success', $pakaian->pakaian_nama . ' masuk ke keranjang.');
  }

  protected function uniquePaymentMethods($methods)
  {
    $seen = [];

    return $methods->filter(function ($method) use (&$seen) {
      $key = $method->metode_pembayaran_jenis === 'COD' && blank($method->metode_pembayaran_nomor)
        ? 'COD'
        : $method->metode_pembayaran_jenis . ':' . ($method->metode_pembayaran_nomor ?? '');

      if (isset($seen[$key])) {
        return false;
      }

      $seen[$key] = true;
      return true;
    })->values();
  }

  public function cart()
  {
    $cart = session('cart', []);
    $products = Pakaian::whereIn('pakaian_id', array_keys($cart))->get();
    $paymentMethods = session('user_id')
      ? $this->uniquePaymentMethods(MetodePembayaran::where('metode_pembayaran_user_id', session('user_id'))->get())
      : collect();
    return view('shop.cart', compact('products', 'cart', 'paymentMethods'));
  }

  public function remove(Pakaian $pakaian)
  {
    $cart = session('cart', []);
    unset($cart[$pakaian->pakaian_id]);
    session(['cart' => $cart]);

    return back()->with('success', $pakaian->pakaian_nama . ' dihapus dari keranjang.');
  }

  public function clear()
  {
    session()->forget('cart');

    return redirect()->route('cart')->with('success', 'Keranjang berhasil dikosongkan.');
  }

  public function checkout(Request $request)
  {
    if (!session('user_id')) {
      return redirect()->route('login')->withErrors(['login' => 'Silakan login atau daftar terlebih dahulu untuk checkout.']);
    }

    $user = User::findOrFail(session('user_id'));
    $cart = session('cart', []);
    $products = Pakaian::whereIn('pakaian_id', array_keys($cart))->get();
    abort_if($products->isEmpty(), 422, 'Keranjang masih kosong.');
    $data = $request->validate([
      'metode_pembayaran_id' => 'required|exists:metode_pembayaran,metode_pembayaran_id',
    ]);
    $payment = MetodePembayaran::where('metode_pembayaran_user_id', $user->user_id)
      ->findOrFail($data['metode_pembayaran_id']);
    $total = $products->sum(fn($item) => (int) $item->pakaian_harga * $cart[$item->pakaian_id]);
    $purchase = Pembelian::create(['pembelian_user_id' => $user->user_id, 'pembelian_metode_pembayaran_id' => $payment->metode_pembayaran_id, 'pembelian_tanggal' => now(), 'pembelian_total_harga' => $total, 'pembelian_status' => 'diproses']);
    foreach ($products as $product) {
      $qty = min($cart[$product->pakaian_id], $product->pakaian_stok);
      PembelianDetail::create(['pembelian_detail_pembelian_id' => $purchase->pembelian_id, 'pembelian_detail_pakaian_id' => $product->pakaian_id, 'pembelian_detail_jumlah' => $qty, 'pembelian_detail_total_harga' => (int) $product->pakaian_harga * $qty]);
      $product->decrement('pakaian_stok', $qty);
    }
    session()->forget('cart');
    return redirect()->route('purchase.receipt', $purchase)->with('success', 'Pesanan berhasil dibuat.');
  }

  public function login()
  {
    return view('shop.login');
  }

  public function register()
  {
    return view('shop.register');
  }

  public function account()
  {
    abort_unless(session('user_id'), 403);
    return view('shop.account', [
      'user' => User::findOrFail(session('user_id')),
      'paymentMethods' => MetodePembayaran::where('metode_pembayaran_user_id', session('user_id'))->get(),
    ]);
  }

  public function purchaseHistory()
  {
    abort_unless(session('user_id'), 403);

    return view('shop.purchase-history', [
      'purchases' => Pembelian::with(['detail.pakaian', 'metodePembayaran'])
        ->where('pembelian_user_id', session('user_id'))
        ->latest('pembelian_id')
        ->get(),
    ]);
  }

  public function purchaseReceipt(Pembelian $pembelian)
  {
    abort_unless($pembelian->pembelian_user_id === session('user_id'), 403);

    return view('shop.purchase-receipt', [
      'purchase' => $pembelian->load(['user', 'detail.pakaian', 'metodePembayaran']),
    ]);
  }

  public function requestCancellation(Pembelian $pembelian)
  {
    abort_unless($pembelian->pembelian_user_id === session('user_id'), 403);
    abort_if($pembelian->pembelian_status !== 'diproses', 422, 'Pesanan hanya dapat dibatalkan saat masih diproses.');

    $pembelian->update(['pembelian_status' => 'menunggu_pembatalan']);

    return back()->with('success', 'Permintaan pembatalan dikirim dan menunggu persetujuan admin.');
  }

  public function storePaymentMethod(Request $request)
  {
    abort_unless(session('user_id'), 403);
    $data = $request->validate([
      'metode_pembayaran_jenis' => 'required|in:DANA,OVO,BCA,COD',
      'metode_pembayaran_nomor' => 'nullable|string|max:50',
    ]);

    $jenis = blank($data['metode_pembayaran_nomor']) ? 'COD' : $data['metode_pembayaran_jenis'];
    $nomor = blank($data['metode_pembayaran_nomor']) ? null : $data['metode_pembayaran_nomor'];

    $existingPayment = MetodePembayaran::where('metode_pembayaran_user_id', session('user_id'))
      ->where('metode_pembayaran_jenis', $jenis)
      ->when($nomor, function ($query, $nomor) {
        $query->where('metode_pembayaran_nomor', $nomor);
      }, function ($query) {
        $query->where(function ($nested) {
          $nested->whereNull('metode_pembayaran_nomor')->orWhere('metode_pembayaran_nomor', '');
        });
      })
      ->first();

    if ($existingPayment) {
      return back()->with('success', 'Metode pembayaran sudah tersedia.');
    }

    MetodePembayaran::create([
      'metode_pembayaran_user_id' => session('user_id'),
      'metode_pembayaran_jenis' => $jenis,
      'metode_pembayaran_nomor' => $nomor,
    ]);
    return back()->with('success', 'Metode pembayaran berhasil disimpan.');
  }

  public function destroyPaymentMethod(MetodePembayaran $metodePembayaran)
  {
    abort_unless($metodePembayaran->metode_pembayaran_user_id === session('user_id'), 403);
    abort_if(Pembelian::where('pembelian_metode_pembayaran_id', $metodePembayaran->metode_pembayaran_id)->exists(), 422, 'Metode pembayaran yang sudah digunakan tidak dapat dihapus.');
    $metodePembayaran->delete();
    return back()->with('success', 'Metode pembayaran dihapus.');
  }

  public function updateAccount(Request $request)
  {
    $user = User::findOrFail(session('user_id'));
    $data = $request->validate([
      'user_fullname' => 'required|string|max:100',
      'user_email' => 'required|email|max:50|unique:user,user_email,' . $user->user_id . ',user_id',
      'user_nohp' => 'required|string|max:13',
      'user_alamat' => 'required|string|max:200',
      'user_profil' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);
    if ($request->hasFile('user_profil')) {
      if (str_starts_with((string) $user->user_profil_url, '/storage/')) {
        Storage::disk('public')->delete(str_replace('/storage/', '', $user->user_profil_url));
      }
      $profilePath = $request->file('user_profil')->store('profile', 'public');
      $data['user_profil_url'] = asset('storage/' . $profilePath);
    }
    unset($data['user_profil']);
    $user->update($data);
    session(['user_profile_url' => $user->user_profil_url]);
    return back()->with('success', 'Profil akun berhasil diperbarui.');
  }

  public function updatePassword(Request $request)
  {
    $user = User::findOrFail(session('user_id'));
    $data = $request->validate([
      'current_password' => 'required|string',
      'password' => 'required|string|min:6|confirmed',
    ]);
    if (!Hash::check($data['current_password'], $user->user_password)) {
      return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
    }
    $user->update(['user_password' => $data['password']]);
    return back()->with('success', 'Password berhasil diganti.');
  }

  public function storeRegistration(Request $request)
  {
    $data = $request->validate([
      'user_username' => 'required|string|min:3|max:50|unique:user,user_username',
      'user_fullname' => 'required|string|max:100',
      'user_email' => 'required|email|max:50|unique:user,user_email',
      'user_nohp' => 'required|string|max:13',
      'user_alamat' => 'required|string|max:200',
      'password' => 'required|string|min:6|confirmed',
    ]);

    $user = User::create([
      'user_username' => $data['user_username'],
      'user_password' => $data['password'],
      'user_fullname' => $data['user_fullname'],
      'user_email' => $data['user_email'],
      'user_nohp' => $data['user_nohp'],
      'user_alamat' => $data['user_alamat'],
      'user_level' => 'Pengguna',
    ]);

    session(['user_id' => $user->user_id, 'user_level' => $user->user_level, 'user_profile_url' => $user->user_profil_url]);

    return redirect()->route('shop')->with('success', 'Akun berhasil dibuat. Selamat datang di Ruang Kedua!');
  }

  public function authenticate(Request $request)
  {
    $user = User::where('user_username', $request->username)->first();
    if (!$user || !password_verify($request->password, $user->user_password)) return back()->withErrors(['username' => 'Username atau password salah.']);
    session(['user_id' => $user->user_id, 'user_level' => $user->user_level, 'user_profile_url' => $user->user_profil_url]);
    return redirect($user->user_level === 'Admin' ? route('admin.dashboard') : route('shop'));
  }
  public function logout()
  {
    session()->forget(['user_id', 'user_level', 'user_profile_url']);
    return redirect()->route('shop');
  }
}
