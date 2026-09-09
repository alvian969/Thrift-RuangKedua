<?php

namespace Database\Seeders;

use App\Models\KategoriPakaian;
use App\Models\MetodePembayaran;
use App\Models\Pakaian;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'user_username' => 'admin',
            'user_password' => 'password',
            'user_fullname' => 'Thrift Admin',
            'user_email' => 'admin@thrift.local',
            'user_nohp' => '081234567890',
            'user_alamat' => 'Gudang Thrift',
            'user_level' => 'Admin',
        ]);

        $customer = User::create([
            'user_username' => 'pelanggan',
            'user_password' => 'password',
            'user_fullname' => 'Pelanggan Thrift',
            'user_email' => 'user@thrift.local',
            'user_nohp' => '081234567891',
            'user_alamat' => 'Jakarta',
            'user_level' => 'Pengguna',
        ]);

        MetodePembayaran::create([
            'metode_pembayaran_user_id' => $customer->user_id,
            'metode_pembayaran_jenis' => 'COD',
        ]);

        $categories = collect(['Outerwear', 'Tops', 'Bottoms', 'Accessories'])->mapWithKeys(fn($name) => [$name => KategoriPakaian::create(['kategori_pakaian_nama' => $name])]);

        $products = [
            ['Outerwear', 'Varsity Jacket Navy', 185000, 4, 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=900'],
            ['Tops', 'Cotton Overshirt', 125000, 7, 'https://images.unsplash.com/photo-1596755389378-c31d21fd1273?w=900'],
            ['Bottoms', 'Denim Relaxed Fit', 150000, 3, 'https://images.unsplash.com/photo-1542272604-787c3835535d?w=900'],
            ['Tops', 'Vintage Graphic Tee', 95000, 8, 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900'],
            ['Outerwear', 'Wool Bomber Jacket', 220000, 2, 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?w=900'],
            ['Bottoms', 'Cargo Trouser Khaki', 170000, 5, 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=900'],
            ['Accessories', 'Canvas Tote Bag', 80000, 6, 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=900'],
            ['Accessories', 'Retro Cap', 70000, 9, 'https://images.unsplash.com/photo-1521369909026-2afed882baee?w=900'],
        ];

        foreach ($products as [$categoryName, $name, $price, $stock, $image]) {
            Pakaian::create([
                'pakaian_kategori_pakaian_id' => $categories[$categoryName]->kategori_pakaian_id,
                'pakaian_nama' => $name,
                'pakaian_harga' => $price,
                'pakaian_stok' => $stock,
                'pakaian_gambar_url' => $image,
            ]);
        }
    }
}
