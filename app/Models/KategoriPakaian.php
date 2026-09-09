<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriPakaian extends Model
{
  protected $table = 'kategori_pakaian';
  protected $primaryKey = 'kategori_pakaian_id';
  public $timestamps = false;
  protected $fillable = ['kategori_pakaian_nama'];

  public function pakaian()
  {
    return $this->hasMany(Pakaian::class, 'pakaian_kategori_pakaian_id', 'kategori_pakaian_id');
  }
}
