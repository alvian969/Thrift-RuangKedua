<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetodePembayaran extends Model
{
  protected $table = 'metode_pembayaran';
  protected $primaryKey = 'metode_pembayaran_id';
  public $timestamps = false;
  protected $fillable = ['metode_pembayaran_user_id', 'metode_pembayaran_jenis', 'metode_pembayaran_nomor'];
}
