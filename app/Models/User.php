<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'user';

    protected $primaryKey = 'user_id';

    public $timestamps = false;

    protected $fillable = [
        'user_username',
        'user_password',
        'user_fullname',
        'user_email',
        'user_nohp',
        'user_alamat',
        'user_profil_url',
        'user_level',
    ];

    public function getAuthPassword(): string
    {
        return $this->user_password;
    }

    public function pembelian()
    {
        return $this->hasMany(Pembelian::class, 'pembelian_user_id', 'user_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'user_password' => 'hashed',
        ];
    }
}
