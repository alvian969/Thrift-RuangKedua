<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pakaian extends Model
{
  protected $table = 'pakaian';
  protected $primaryKey = 'pakaian_id';
  public $timestamps = false;
  protected $fillable = ['pakaian_kategori_pakaian_id', 'pakaian_nama', 'pakaian_harga', 'pakaian_stok', 'pakaian_gambar_url'];

  public function kategori()
  {
    return $this->belongsTo(KategoriPakaian::class, 'pakaian_kategori_pakaian_id', 'kategori_pakaian_id');
  }
}
