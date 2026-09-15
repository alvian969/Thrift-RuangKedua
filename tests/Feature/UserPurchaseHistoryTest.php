<?php

namespace Tests\Feature;

use App\Models\KategoriPakaian;
use App\Models\MetodePembayaran;
use App\Models\Pakaian;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPurchaseHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_blank_payment_number_is_saved_as_cod(): void
    {
        $user = User::create([
            'user_username' => 'paymentuser',
            'user_password' => 'secret123',
            'user_fullname' => 'Payment User',
            'user_email' => 'payment@example.com',
            'user_nohp' => '081234567890',
            'user_alamat' => 'Jl. Test No. 2',
            'user_profil_url' => 'https://example.com/profile.jpg',
            'user_level' => 'Pengguna',
        ]);

        session(['user_id' => $user->user_id]);

        $this->post('/akun/metode-pembayaran', [
            'metode_pembayaran_jenis' => 'DANA',
            'metode_pembayaran_nomor' => '',
        ]);

        $this->assertDatabaseHas('metode_pembayaran', [
            'metode_pembayaran_user_id' => $user->user_id,
            'metode_pembayaran_jenis' => 'COD',
            'metode_pembayaran_nomor' => null,
        ]);
    }

    public function test_duplicate_cod_method_is_not_created_twice(): void
    {
        $user = User::create([
            'user_username' => 'coddup',
            'user_password' => 'secret123',
            'user_fullname' => 'COD Duplicate',
            'user_email' => 'coddup@example.com',
            'user_nohp' => '081234567891',
            'user_alamat' => 'Jl. Test No. 3',
            'user_profil_url' => 'https://example.com/profile.jpg',
            'user_level' => 'Pengguna',
        ]);

        session(['user_id' => $user->user_id]);

        MetodePembayaran::create([
            'metode_pembayaran_user_id' => $user->user_id,
            'metode_pembayaran_jenis' => 'COD',
            'metode_pembayaran_nomor' => null,
        ]);

        $this->post('/akun/metode-pembayaran', [
            'metode_pembayaran_jenis' => 'DANA',
            'metode_pembayaran_nomor' => '',
        ]);

        $this->assertSame(1, MetodePembayaran::where('metode_pembayaran_user_id', $user->user_id)
            ->where('metode_pembayaran_jenis', 'COD')
            ->whereNull('metode_pembayaran_nomor')
            ->count());
    }

    public function test_admin_can_update_product_data(): void
    {
        $category = KategoriPakaian::create([
            'kategori_pakaian_nama' => 'Kaos',
        ]);

        $user = User::create([
            'user_username' => 'adminedit',
            'user_password' => 'secret123',
            'user_fullname' => 'Admin Edit',
            'user_email' => 'adminedit@example.com',
            'user_nohp' => '081234567892',
            'user_alamat' => 'Jl. Admin No. 1',
            'user_profile_url' => 'https://example.com/admin.jpg',
            'user_level' => 'Admin',
        ]);

        $product = Pakaian::create([
            'pakaian_kategori_pakaian_id' => $category->kategori_pakaian_id,
            'pakaian_nama' => 'Kaos Lama',
            'pakaian_harga' => 50000,
            'pakaian_stok' => 2,
            'pakaian_gambar_url' => 'https://example.com/old.jpg',
        ]);

        session(['user_level' => 'Admin']);

        $response = $this->put('/admin/pakaian/' . $product->pakaian_id, [
            'pakaian_kategori_pakaian_id' => $category->kategori_pakaian_id,
            'pakaian_nama' => 'Kaos Baru',
            'pakaian_harga' => 75000,
            'pakaian_stok' => 5,
            'pakaian_gambar_url' => 'https://example.com/new.jpg',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pakaian', [
            'pakaian_id' => $product->pakaian_id,
            'pakaian_nama' => 'Kaos Baru',
            'pakaian_harga' => 75000,
            'pakaian_stok' => 5,
            'pakaian_gambar_url' => 'https://example.com/new.jpg',
        ]);
    }

    public function test_admin_gets_notification_when_deleting_product_in_active_purchase(): void
    {
        $category = KategoriPakaian::create(['kategori_pakaian_nama' => 'Kaos']);
        $admin = User::create([
            'user_username' => 'admindeleteactive',
            'user_password' => 'secret123',
            'user_fullname' => 'Admin Delete Active',
            'user_email' => 'admindeleteactive@example.com',
            'user_nohp' => '081234567800',
            'user_alamat' => 'Jl. Admin No. 5',
            'user_profil_url' => 'https://example.com/admin-delete.jpg',
            'user_level' => 'Admin',
        ]);
        $buyer = User::create([
            'user_username' => 'buyeractive',
            'user_password' => 'secret123',
            'user_fullname' => 'Buyer Active',
            'user_email' => 'buyeractive@example.com',
            'user_nohp' => '081234567801',
            'user_alamat' => 'Jl. Pembeli No. 5',
            'user_profil_url' => 'https://example.com/buyer-active.jpg',
            'user_level' => 'Pengguna',
        ]);
        $payment = MetodePembayaran::create([
            'metode_pembayaran_user_id' => $buyer->user_id,
            'metode_pembayaran_jenis' => 'COD',
        ]);
        $product = Pakaian::create([
            'pakaian_kategori_pakaian_id' => $category->kategori_pakaian_id,
            'pakaian_nama' => 'Kaos Aktif',
            'pakaian_harga' => 50000,
            'pakaian_stok' => 1,
            'pakaian_gambar_url' => 'https://example.com/aktif.jpg',
        ]);
        $purchase = Pembelian::create([
            'pembelian_user_id' => $buyer->user_id,
            'pembelian_metode_pembayaran_id' => $payment->metode_pembayaran_id,
            'pembelian_tanggal' => now(),
            'pembelian_total_harga' => 50000,
            'pembelian_status' => 'diproses',
        ]);
        PembelianDetail::create([
            'pembelian_detail_pembelian_id' => $purchase->pembelian_id,
            'pembelian_detail_pakaian_id' => $product->pakaian_id,
            'pembelian_detail_jumlah' => 1,
            'pembelian_detail_total_harga' => 50000,
        ]);

        session(['user_id' => $admin->user_id, 'user_level' => 'Admin']);
        $response = $this->delete('/admin/pakaian/' . $product->pakaian_id);

        $response->assertRedirect()->assertSessionHas('error', 'Barang yang masih dalam proses pembelian tidak dapat dihapus.');
        $this->assertDatabaseHas('pakaian', ['pakaian_id' => $product->pakaian_id]);
    }

    public function test_admin_can_delete_product_from_completed_purchase(): void
    {
        $category = KategoriPakaian::create(['kategori_pakaian_nama' => 'Kaos']);
        $admin = User::create([
            'user_username' => 'admindeletecompleted',
            'user_password' => 'secret123',
            'user_fullname' => 'Admin Delete Completed',
            'user_email' => 'admindeletecompleted@example.com',
            'user_nohp' => '081234567802',
            'user_alamat' => 'Jl. Admin No. 6',
            'user_profil_url' => 'https://example.com/admin-completed.jpg',
            'user_level' => 'Admin',
        ]);
        $buyer = User::create([
            'user_username' => 'buyercompleted',
            'user_password' => 'secret123',
            'user_fullname' => 'Buyer Completed',
            'user_email' => 'buyercompleted@example.com',
            'user_nohp' => '081234567803',
            'user_alamat' => 'Jl. Pembeli No. 6',
            'user_profil_url' => 'https://example.com/buyer-completed.jpg',
            'user_level' => 'Pengguna',
        ]);
        $payment = MetodePembayaran::create([
            'metode_pembayaran_user_id' => $buyer->user_id,
            'metode_pembayaran_jenis' => 'COD',
        ]);
        $product = Pakaian::create([
            'pakaian_kategori_pakaian_id' => $category->kategori_pakaian_id,
            'pakaian_nama' => 'Kaos Selesai',
            'pakaian_harga' => 55000,
            'pakaian_stok' => 0,
            'pakaian_gambar_url' => 'https://example.com/selesai.jpg',
        ]);
        $purchase = Pembelian::create([
            'pembelian_user_id' => $buyer->user_id,
            'pembelian_metode_pembayaran_id' => $payment->metode_pembayaran_id,
            'pembelian_tanggal' => now(),
            'pembelian_total_harga' => 55000,
            'pembelian_status' => 'selesai',
        ]);
        PembelianDetail::create([
            'pembelian_detail_pembelian_id' => $purchase->pembelian_id,
            'pembelian_detail_pakaian_id' => $product->pakaian_id,
            'pembelian_detail_nama_pakaian' => $product->pakaian_nama,
            'pembelian_detail_jumlah' => 1,
            'pembelian_detail_total_harga' => 55000,
        ]);

        session(['user_id' => $admin->user_id, 'user_level' => 'Admin']);
        $this->delete('/admin/pakaian/' . $product->pakaian_id)->assertRedirect();

        $this->assertDatabaseMissing('pakaian', ['pakaian_id' => $product->pakaian_id]);
        $this->assertDatabaseHas('pembelian_detail', [
            'pembelian_detail_pembelian_id' => $purchase->pembelian_id,
            'pembelian_detail_nama_pakaian' => 'Kaos Selesai',
        ]);
    }

    public function test_admin_can_manage_categories_and_cannot_delete_used_category(): void
    {
        $admin = User::create([
            'user_username' => 'admincategory',
            'user_password' => 'secret123',
            'user_fullname' => 'Admin Category',
            'user_email' => 'admincategory@example.com',
            'user_nohp' => '081234567899',
            'user_alamat' => 'Jl. Admin Kategori',
            'user_profil_url' => 'https://example.com/admin-category.jpg',
            'user_level' => 'Admin',
        ]);
        session(['user_id' => $admin->user_id, 'user_level' => 'Admin']);

        $this->post('/admin/kategori', [
            'kategori_pakaian_nama' => 'Jaket',
        ])->assertRedirect();

        $category = KategoriPakaian::where('kategori_pakaian_nama', 'Jaket')->firstOrFail();
        $this->put('/admin/kategori/' . $category->kategori_pakaian_id, [
            'kategori_pakaian_nama' => 'Jaket Vintage',
        ])->assertRedirect();

        $product = Pakaian::create([
            'pakaian_kategori_pakaian_id' => $category->kategori_pakaian_id,
            'pakaian_nama' => 'Jaket Klasik',
            'pakaian_harga' => 120000,
            'pakaian_stok' => 2,
            'pakaian_gambar_url' => 'https://example.com/jaket.jpg',
        ]);

        $this->delete('/admin/kategori/' . $category->kategori_pakaian_id)->assertStatus(422);
        $this->assertDatabaseHas('pakaian', ['pakaian_id' => $product->pakaian_id]);
    }

    public function test_buyer_can_request_cancellation_and_admin_can_approve_it(): void
    {
        $category = KategoriPakaian::create(['kategori_pakaian_nama' => 'Kaos']);
        $buyer = User::create([
            'user_username' => 'buyerstatus',
            'user_password' => 'secret123',
            'user_fullname' => 'Buyer Status',
            'user_email' => 'buyerstatus@example.com',
            'user_nohp' => '081234567893',
            'user_alamat' => 'Jl. Pembeli No. 1',
            'user_profil_url' => 'https://example.com/buyer.jpg',
            'user_level' => 'Pengguna',
        ]);
        $admin = User::create([
            'user_username' => 'adminstatus',
            'user_password' => 'secret123',
            'user_fullname' => 'Admin Status',
            'user_email' => 'adminstatus@example.com',
            'user_nohp' => '081234567894',
            'user_alamat' => 'Jl. Admin No. 2',
            'user_profil_url' => 'https://example.com/admin-status.jpg',
            'user_level' => 'Admin',
        ]);
        $payment = MetodePembayaran::create([
            'metode_pembayaran_user_id' => $buyer->user_id,
            'metode_pembayaran_jenis' => 'COD',
        ]);
        $product = Pakaian::create([
            'pakaian_kategori_pakaian_id' => $category->kategori_pakaian_id,
            'pakaian_nama' => 'Kaos Status',
            'pakaian_harga' => 90000,
            'pakaian_stok' => 3,
            'pakaian_gambar_url' => 'https://example.com/status.jpg',
        ]);
        $purchase = Pembelian::create([
            'pembelian_user_id' => $buyer->user_id,
            'pembelian_metode_pembayaran_id' => $payment->metode_pembayaran_id,
            'pembelian_tanggal' => now(),
            'pembelian_total_harga' => 90000,
            'pembelian_status' => 'diproses',
        ]);
        PembelianDetail::create([
            'pembelian_detail_pembelian_id' => $purchase->pembelian_id,
            'pembelian_detail_pakaian_id' => $product->pakaian_id,
            'pembelian_detail_jumlah' => 1,
            'pembelian_detail_total_harga' => 90000,
        ]);
        $product->decrement('pakaian_stok');

        session(['user_id' => $buyer->user_id, 'user_level' => 'Pengguna']);
        $this->post('/akun/pembelian/' . $purchase->pembelian_id . '/batalkan')->assertRedirect();
        $this->assertDatabaseHas('pembelian', [
            'pembelian_id' => $purchase->pembelian_id,
            'pembelian_status' => 'menunggu_pembatalan',
        ]);

        session(['user_id' => $admin->user_id, 'user_level' => 'Admin']);
        $this->post('/admin/pembelian/' . $purchase->pembelian_id . '/pembatalan', [
            'keputusan' => 'setujui',
        ])->assertRedirect();

        $this->assertDatabaseHas('pembelian', [
            'pembelian_id' => $purchase->pembelian_id,
            'pembelian_status' => 'dibatalkan',
        ]);
        $this->assertDatabaseHas('pakaian', [
            'pakaian_id' => $product->pakaian_id,
            'pakaian_stok' => 3,
        ]);
    }

    public function test_admin_can_change_purchase_status(): void
    {
        $admin = User::create([
            'user_username' => 'adminchange',
            'user_password' => 'secret123',
            'user_fullname' => 'Admin Change',
            'user_email' => 'adminchange@example.com',
            'user_nohp' => '081234567895',
            'user_alamat' => 'Jl. Admin No. 3',
            'user_profil_url' => 'https://example.com/admin-change.jpg',
            'user_level' => 'Admin',
        ]);
        $buyer = User::create([
            'user_username' => 'buyerchange',
            'user_password' => 'secret123',
            'user_fullname' => 'Buyer Change',
            'user_email' => 'buyerchange@example.com',
            'user_nohp' => '081234567896',
            'user_alamat' => 'Jl. Pembeli No. 2',
            'user_profil_url' => 'https://example.com/buyer-change.jpg',
            'user_level' => 'Pengguna',
        ]);
        $payment = MetodePembayaran::create([
            'metode_pembayaran_user_id' => $buyer->user_id,
            'metode_pembayaran_jenis' => 'COD',
        ]);
        $purchase = Pembelian::create([
            'pembelian_user_id' => $buyer->user_id,
            'pembelian_metode_pembayaran_id' => $payment->metode_pembayaran_id,
            'pembelian_tanggal' => now(),
            'pembelian_total_harga' => 100000,
            'pembelian_status' => 'diproses',
        ]);

        session(['user_id' => $admin->user_id, 'user_level' => 'Admin']);
        $this->put('/admin/pembelian/' . $purchase->pembelian_id . '/status', [
            'pembelian_status' => 'dikirim',
        ])->assertRedirect();

        $this->assertDatabaseHas('pembelian', [
            'pembelian_id' => $purchase->pembelian_id,
            'pembelian_status' => 'dikirim',
        ]);
    }

    public function test_direct_admin_cancellation_restores_stock(): void
    {
        $category = KategoriPakaian::create(['kategori_pakaian_nama' => 'Kaos']);
        $admin = User::create([
            'user_username' => 'admindirectcancel',
            'user_password' => 'secret123',
            'user_fullname' => 'Admin Direct Cancel',
            'user_email' => 'admindirectcancel@example.com',
            'user_nohp' => '081234567897',
            'user_alamat' => 'Jl. Admin No. 4',
            'user_profil_url' => 'https://example.com/admin-direct.jpg',
            'user_level' => 'Admin',
        ]);
        $buyer = User::create([
            'user_username' => 'buyerdirectcancel',
            'user_password' => 'secret123',
            'user_fullname' => 'Buyer Direct Cancel',
            'user_email' => 'buyerdirectcancel@example.com',
            'user_nohp' => '081234567898',
            'user_alamat' => 'Jl. Pembeli No. 4',
            'user_profil_url' => 'https://example.com/buyer-direct.jpg',
            'user_level' => 'Pengguna',
        ]);
        $payment = MetodePembayaran::create([
            'metode_pembayaran_user_id' => $buyer->user_id,
            'metode_pembayaran_jenis' => 'COD',
        ]);
        $product = Pakaian::create([
            'pakaian_kategori_pakaian_id' => $category->kategori_pakaian_id,
            'pakaian_nama' => 'Kaos Direct Cancel',
            'pakaian_harga' => 60000,
            'pakaian_stok' => 4,
            'pakaian_gambar_url' => 'https://example.com/direct-cancel.jpg',
        ]);
        $purchase = Pembelian::create([
            'pembelian_user_id' => $buyer->user_id,
            'pembelian_metode_pembayaran_id' => $payment->metode_pembayaran_id,
            'pembelian_tanggal' => now(),
            'pembelian_total_harga' => 60000,
            'pembelian_status' => 'diproses',
        ]);
        PembelianDetail::create([
            'pembelian_detail_pembelian_id' => $purchase->pembelian_id,
            'pembelian_detail_pakaian_id' => $product->pakaian_id,
            'pembelian_detail_jumlah' => 1,
            'pembelian_detail_total_harga' => 60000,
        ]);
        $product->decrement('pakaian_stok');

        session(['user_id' => $admin->user_id, 'user_level' => 'Admin']);
        $this->put('/admin/pembelian/' . $purchase->pembelian_id . '/status', [
            'pembelian_status' => 'dibatalkan',
        ])->assertRedirect();

        $this->assertDatabaseHas('pakaian', [
            'pakaian_id' => $product->pakaian_id,
            'pakaian_stok' => 4,
        ]);
    }

    public function test_user_can_view_purchase_history_on_account_page(): void
    {
        $category = KategoriPakaian::create([
            'kategori_pakaian_nama' => 'Kaos',
        ]);

        $user = User::create([
            'user_username' => 'tester',
            'user_password' => 'secret123',
            'user_fullname' => 'Tester User',
            'user_email' => 'tester@example.com',
            'user_nohp' => '081234567890',
            'user_alamat' => 'Jl. Test No. 1',
            'user_profil_url' => 'https://example.com/profile.jpg',
            'user_level' => 'Pengguna',
        ]);

        $payment = MetodePembayaran::create([
            'metode_pembayaran_user_id' => $user->user_id,
            'metode_pembayaran_jenis' => 'DANA',
            'metode_pembayaran_nomor' => '081234567890',
        ]);

        $product = Pakaian::create([
            'pakaian_kategori_pakaian_id' => $category->kategori_pakaian_id,
            'pakaian_nama' => 'Kaos Oversize',
            'pakaian_harga' => '75000',
            'pakaian_stok' => 5,
            'pakaian_gambar_url' => 'https://example.com/kaos.jpg',
        ]);

        $purchase = Pembelian::create([
            'pembelian_user_id' => $user->user_id,
            'pembelian_metode_pembayaran_id' => $payment->metode_pembayaran_id,
            'pembelian_tanggal' => now(),
            'pembelian_total_harga' => 150000,
        ]);

        PembelianDetail::create([
            'pembelian_detail_pembelian_id' => $purchase->pembelian_id,
            'pembelian_detail_pakaian_id' => $product->pakaian_id,
            'pembelian_detail_jumlah' => 2,
            'pembelian_detail_total_harga' => 150000,
        ]);

        session([
            'user_id' => $user->user_id,
            'user_level' => 'Pengguna',
            'user_profile_url' => 'https://example.com/profile.jpg',
        ]);

        $accountResponse = $this->get('/akun');

        $accountResponse->assertStatus(200)
            ->assertDontSee('Histori Pembelian');

        $response = $this->get('/riwayat-pembelian');

        $response->assertStatus(200)
            ->assertSee('Histori')
            ->assertSee('pembelian.')
            ->assertSee('Kaos Oversize')
            ->assertSee('Rp 150.000');

        $receiptResponse = $this->get('/riwayat-pembelian/' . $purchase->pembelian_id . '/nota');

        $receiptResponse->assertStatus(200)
            ->assertSee('Nota')
            ->assertSee('Kaos Oversize')
            ->assertSee('Rp 150.000')
            ->assertSee('DANA');
    }
}
