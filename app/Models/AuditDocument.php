<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditDocument extends Model
{
    use HasFactory;

    protected $table = 'audit_documents';

    protected $fillable = [
        'audit_finding_id',
        'document_name',
        'file_path',
        'upload_date',
        'uploaded_by',
    ];

    protected $casts = [
        'upload_date' => 'datetime',
    ];

    public function auditFinding(): BelongsTo
    {
        return $this->belongsTo(AuditFinding::class, 'audit_finding_id');
    }

    public function uploadedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
