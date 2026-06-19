<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Banding extends Model
{
    use HasFactory;

    protected $table = 'bandings';

    protected $fillable = [
        'user_id',
        'pengajuan_id',
        'jenis',
        'pesan',
        'file_lampiran',
        'status',
        'catatan_admin',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
    }
}
