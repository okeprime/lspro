<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AuditFinding extends Model
{
    use HasFactory;

    protected $table = 'audit_findings';

    protected $fillable = [
        'pengajuan_id',
        'audit_type',
        'finding_number',
        'description',
        'severity',
        'action_required',
        'status',
        'pic_responsible_id',
    ];

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
    }

    public function picResponsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_responsible_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(AuditDocument::class, 'audit_finding_id');
    }
}
