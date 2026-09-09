<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
  protected $table = 'pembelian';
  protected $primaryKey = 'pembelian_id';
  public $timestamps = false;
  protected $fillable = ['pembelian_user_id', 'pembelian_metode_pembayaran_id', 'pembelian_tanggal', 'pembelian_total_harga', 'pembelian_status'];
  protected $casts = ['pembelian_tanggal' => 'datetime'];

  public function detail()
  {
    return $this->hasMany(PembelianDetail::class, 'pembelian_detail_pembelian_id', 'pembelian_id');
  }
  public function user()
  {
    return $this->belongsTo(User::class, 'pembelian_user_id', 'user_id');
  }
  public function metodePembayaran()
  {
    return $this->belongsTo(MetodePembayaran::class, 'pembelian_metode_pembayaran_id', 'metode_pembayaran_id');
  }
}
