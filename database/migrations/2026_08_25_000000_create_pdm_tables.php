<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('kategori_pakaian', function (Blueprint $table) {
      $table->id('kategori_pakaian_id');
      $table->string('kategori_pakaian_nama', 50);
    });

    Schema::create('pakaian', function (Blueprint $table) {
      $table->id('pakaian_id');
      $table->foreignId('pakaian_kategori_pakaian_id')
        ->constrained('kategori_pakaian', 'kategori_pakaian_id');
      $table->string('pakaian_nama', 50);
      $table->string('pakaian_harga', 50);
      $table->integer('pakaian_stok');
      $table->string('pakaian_gambar_url', 255);
    });

    Schema::create('metode_pembayaran', function (Blueprint $table) {
      $table->id('metode_pembayaran_id');
      $table->foreignId('metode_pembayaran_user_id')
        ->constrained('user', 'user_id');
      $table->enum('metode_pembayaran_jenis', ['DANA', 'OVO', 'BCA', 'COD']);
      $table->string('metode_pembayaran_nomor', 50)->nullable();
    });

    Schema::create('pembelian', function (Blueprint $table) {
      $table->id('pembelian_id');
      $table->foreignId('pembelian_user_id')->constrained('user', 'user_id');
      $table->foreignId('pembelian_metode_pembayaran_id')
        ->constrained('metode_pembayaran', 'metode_pembayaran_id');
      $table->timestamp('pembelian_tanggal');
      $table->integer('pembelian_total_harga');
    });

    Schema::create('pembelian_detail', function (Blueprint $table) {
      $table->id('pembelian_detail_id');
      $table->foreignId('pembelian_detail_pembelian_id')
        ->constrained('pembelian', 'pembelian_id');
      $table->foreignId('pembelian_detail_pakaian_id')
        ->constrained('pakaian', 'pakaian_id');
      $table->integer('pembelian_detail_jumlah');
      $table->integer('pembelian_detail_total_harga');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('pembelian_detail');
    Schema::dropIfExists('pembelian');
    Schema::dropIfExists('metode_pembayaran');
    Schema::dropIfExists('pakaian');
    Schema::dropIfExists('kategori_pakaian');
  }
};
