<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResertifikasiScopeChange extends Model
{
    use HasFactory;

    protected $table = 'resertifikasi_scope_changes';

    protected $fillable = [
        'pengajuan_id',
        'tipe_perubahan',
        'scope_description_old',
        'scope_description_new',
        'alasan_perubahan',
        'surat_perubahan_file',
        'dokumen_pendukung_file',
        'status',
        'rejection_reason',
    ];

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
    }
}
