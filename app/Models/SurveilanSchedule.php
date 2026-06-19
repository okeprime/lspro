<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveilanSchedule extends Model
{
    use HasFactory;

    protected $table = 'survailen_schedules';

    protected $fillable = [
        'pengajuan_id',
        'user_id',
        'survailen_year',
        'reminder_month',
        'reminder_sent',
        'last_reminder_date',
        'deadline',
        'catatan',
        'status_dokumen',
        'file_dokumen',
        'status',
    ];

    protected $casts = [
        'reminder_sent' => 'boolean',
        'last_reminder_date' => 'datetime',
        'deadline' => 'date',
    ];

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
