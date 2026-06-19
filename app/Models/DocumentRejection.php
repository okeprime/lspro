<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentRejection extends Model
{
    use HasFactory;

    protected $table = 'document_rejections';

    protected $fillable = [
        'pengajuan_id',
        'document_category',
        'document_name',
        'rejection_reason',
        'catatan_kurang',
        'catatan_tu',
        'rejected_at',
        'resubmitted_at',
        'status',
    ];

    protected $casts = [
        'rejected_at' => 'datetime',
        'resubmitted_at' => 'datetime',
    ];

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
    }
}
