<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ShopController::class, 'index'])->name('shop');
Route::get('/produk/{pakaian}', [ShopController::class, 'show'])->name('product.show');
Route::post('/cart/{pakaian}', [ShopController::class, 'add'])->name('cart.add');
Route::delete('/cart/{pakaian}', [ShopController::class, 'remove'])->name('cart.remove');
Route::delete('/cart', [ShopController::class, 'clear'])->name('cart.clear');
Route::get('/cart', [ShopController::class, 'cart'])->name('cart');
Route::post('/checkout', [ShopController::class, 'checkout'])->name('checkout');
Route::get('/login', [ShopController::class, 'login'])->name('login');
Route::post('/login', [ShopController::class, 'authenticate'])->name('login.store');
Route::get('/register', [ShopController::class, 'register'])->name('register');
Route::post('/register', [ShopController::class, 'storeRegistration'])->name('register.store');
Route::get('/akun', [ShopController::class, 'account'])->name('account');
Route::get('/riwayat-pembelian', [ShopController::class, 'purchaseHistory'])->name('purchase.history');
Route::get('/riwayat-pembelian/{pembelian}/nota', [ShopController::class, 'purchaseReceipt'])->name('purchase.receipt');
Route::post('/akun/pembelian/{pembelian}/batalkan', [ShopController::class, 'requestCancellation'])->name('purchase.cancel');
Route::put('/akun', [ShopController::class, 'updateAccount'])->name('account.update');
Route::put('/akun/password', [ShopController::class, 'updatePassword'])->name('account.password');
Route::post('/akun/metode-pembayaran', [ShopController::class, 'storePaymentMethod'])->name('payment.store');
Route::delete('/akun/metode-pembayaran/{metodePembayaran}', [ShopController::class, 'destroyPaymentMethod'])->name('payment.destroy');
Route::post('/logout', [ShopController::class, 'logout'])->name('logout');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/pakaian', [AdminController::class, 'pakaian'])->name('pakaian');
    Route::post('/pakaian', [AdminController::class, 'storePakaian'])->name('pakaian.store');
    Route::put('/pakaian/{pakaian}', [AdminController::class, 'updatePakaian'])->name('pakaian.update');
    Route::delete('/pakaian/{pakaian}', [AdminController::class, 'destroyPakaian'])->name('pakaian.destroy');
    Route::get('/pembelian', [AdminController::class, 'pembelian'])->name('pembelian');
    Route::put('/pembelian/{pembelian}/status', [AdminController::class, 'updateStatus'])->name('pembelian.status');
    Route::post('/pembelian/{pembelian}/pembatalan', [AdminController::class, 'handleCancellation'])->name('pembelian.pembatalan');
    Route::get('/pengguna', [AdminController::class, 'pengguna'])->name('pengguna');
});
