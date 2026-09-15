# Ruang Kedua

Ruang Kedua adalah aplikasi web toko pakaian thrift/preloved yang dibangun dengan Laravel. Pengunjung dapat melihat katalog pakaian, mencari dan memfilter produk, memasukkan barang ke keranjang, lalu melakukan checkout. Admin dapat mengelola produk, kategori, pesanan, dan pengguna.

## Fitur

- Katalog dan detail produk pakaian
- Pencarian dan filter produk berdasarkan kategori
- Keranjang belanja berbasis session
- Registrasi, login, dan pengelolaan profil
- Checkout dengan pilihan DANA, OVO, BCA, dan COD
- Riwayat pembelian dan nota transaksi
- Pengajuan pembatalan pesanan
- Dashboard admin untuk mengelola produk, kategori, pesanan, stok, dan pengguna

## Teknologi

- PHP 8.3+
- Laravel 13
- SQLite (default) atau MySQL
- Vite
- Tailwind CSS
- PHPUnit

## Persyaratan

Pastikan perangkat sudah memiliki:

- PHP 8.3 atau lebih baru
- Composer
- Node.js dan npm
- SQLite atau MySQL

## Instalasi

1. Clone repository dan masuk ke folder project:

   ```bash
   git clone <URL-REPOSITORY>
   cd laravel
   ```

2. Install dependency PHP:

   ```bash
   composer install
   ```

3. Buat file environment dan application key.

   Windows PowerShell:

   ```powershell
   Copy-Item .env.example .env
   php artisan key:generate
   ```

   Linux/macOS:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Atur koneksi database pada file `.env`. Project menggunakan SQLite secara default. Pastikan file `database/database.sqlite` tersedia. Pada Windows, file tersebut dapat dibuat secara manual jika belum tersedia.

5. Jalankan migration, data awal, dan symbolic link storage:

   ```bash
   php artisan migrate --seed
   php artisan storage:link
   ```

6. Install dependency frontend:

   ```bash
   npm install
   ```

## Menjalankan Aplikasi

Jalankan server Laravel:

```bash
php artisan serve
```

Pada terminal lain, jalankan Vite:

```bash
npm run dev
```

Buka [http://localhost:8000](http://localhost:8000) pada browser.

Untuk build asset frontend:

```bash
npm run build
```

## Akun Demo

Akun berikut dibuat oleh seeder dan hanya ditujukan untuk development:

| Level | Username | Password |
| --- | --- | --- |
| Admin | `admin` | `password` |
| Pelanggan | `pelanggan` | `password` |

Jangan gunakan kredensial demo ini pada environment production.

## Testing

Jalankan seluruh test dengan:

```bash
php artisan test
```

Atau gunakan script Composer:

```bash
composer test
```

Test menggunakan SQLite in-memory sehingga tidak mengubah database development.

## Struktur Utama

```text
app/                 Logika aplikasi, controller, dan model
database/migrations/ Struktur tabel database
database/seeders/    Data awal aplikasi
resources/views/     Template halaman Blade
resources/js/        Asset JavaScript
resources/css/       Asset CSS
routes/web.php       Daftar route web
tests/               Automated tests
```

## Lisensi

Project ini dibuat untuk kebutuhan pembelajaran dan pengembangan aplikasi toko pakaian thrift.
