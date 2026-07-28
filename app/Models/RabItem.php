<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RabItem extends Model
{
    protected $fillable = [
        'pengajuan_id',
        'kategori',
        'komponen',
        'hari',
        'orang',
        'tarif_pnbp_satuan',
        'tarif_pnbp_total',
        'izin_penggunaan',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }
}
