<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::table('pembelian', function (Blueprint $table) {
      $table->enum('pembelian_status', [
        'diproses',
        'menunggu_pembatalan',
        'dikirim',
        'selesai',
        'dibatalkan',
      ])->default('diproses')->after('pembelian_total_harga');
    });
  }

  public function down(): void
  {
    Schema::table('pembelian', function (Blueprint $table) {
      $table->dropColumn('pembelian_status');
    });
  }
};
