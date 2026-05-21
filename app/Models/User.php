<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'nama_perusahaan',
        'nama_penghubung',
        'no_telp',
        'email',
        'password',
        'alamat',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}