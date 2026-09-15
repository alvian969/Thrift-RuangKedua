<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::table('pembelian_detail', function (Blueprint $table) {
      $table->string('pembelian_detail_nama_pakaian', 50)->nullable()->after('pembelian_detail_pakaian_id');
    });

    DB::statement('UPDATE pembelian_detail SET pembelian_detail_nama_pakaian = (SELECT pakaian_nama FROM pakaian WHERE pakaian.pakaian_id = pembelian_detail.pembelian_detail_pakaian_id)');

    Schema::table('pembelian_detail', function (Blueprint $table) {
      $table->dropForeign(['pembelian_detail_pakaian_id']);
      $table->unsignedBigInteger('pembelian_detail_pakaian_id')->nullable()->change();
      $table->foreign('pembelian_detail_pakaian_id')
        ->references('pakaian_id')
        ->on('pakaian')
        ->nullOnDelete();
    });
  }

  public function down(): void
  {
    Schema::table('pembelian_detail', function (Blueprint $table) {
      $table->dropForeign(['pembelian_detail_pakaian_id']);
      $table->unsignedBigInteger('pembelian_detail_pakaian_id')->nullable(false)->change();
      $table->foreign('pembelian_detail_pakaian_id')
        ->references('pakaian_id')
        ->on('pakaian');
      $table->dropColumn('pembelian_detail_nama_pakaian');
    });
  }
};
