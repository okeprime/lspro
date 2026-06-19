<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
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
        'sub_role',
        'nip',
        'unit_kerja',
        'jabatan',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}