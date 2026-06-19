<?php

namespace App\Models;

use App\Support\LsproType5Workflow;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pengajuan extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan oleh model di dalam database.
     */
    protected $table = 'pengajuans';

    /**
     * Atribut yang dapat diisi secara massal (Mass Assignable).
     */
    protected $fillable = [
        'user_id',
        'tahap',
        'nomor_registrasi',
        'jenis_pengajuan',
        'jenis_sertifikasi',
        'status',
        'keterangan_admin',
        'data_form',
        'file_permohonan',
        'file_permohonan_ttd',
        'ceklis_dokumen',
        'catatan',
        'nama_tu',
    ];

    /**
     * Casting tipe data kolom saat berinteraksi dengan database.
     */
    protected $casts = [
        'data_form' => 'array',
    ];

    /**
     * ========================================
     * RELATIONSHIPS
     * ========================================
     */

    /**
     * Relasi ke User (Pemilik Pengajuan)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Invoices (Satu pengajuan bisa punya banyak invoice)
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'pengajuan_id');
    }

    /**
     * Relasi ke Invoice terbaru (HasOne shortcut)
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'pengajuan_id')->latestOfMany();
    }

    /**
     * Relasi ke Document Rejections
     */
    public function documentRejections(): HasMany
    {
        return $this->hasMany(DocumentRejection::class, 'pengajuan_id');
    }

    /**
     * Relasi ke Audit Findings (Audit kecukupan & kesesuaian)
     */
    public function auditFindings(): HasMany
    {
        return $this->hasMany(AuditFinding::class, 'pengajuan_id');
    }

    /**
     * Relasi ke Survailen Schedule
     */
    public function surveilanSchedule(): HasOne
    {
        return $this->hasOne(SurveilanSchedule::class, 'pengajuan_id');
    }

    /**
     * Relasi ke Resertifikasi Scope Changes
     */
    public function scopeChanges(): HasMany
    {
        return $this->hasMany(ResertifikasiScopeChange::class, 'pengajuan_id');
    }

    /**
     * Relasi ke Notifications
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'pengajuan_id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(PengajuanStatusHistory::class)->latest();
    }

    /**
     * ========================================
     * HELPER METHODS
     * ========================================
     */

    /**
     * Cek apakah ini adalah pengajuan sertifikasi awal
     */
    public function isSertifikasi(): bool
    {
        return strtolower($this->jenis_pengajuan) === 'sertifikasi';
    }

    /**
     * Cek apakah ini adalah pengajuan survailen
     */
    public function isSurvailen(): bool
    {
        return strtolower($this->jenis_pengajuan) === 'survailen';
    }

    /**
     * Cek apakah ini adalah pengajuan resertifikasi
     */
    public function isResertifikasi(): bool
    {
        return strtolower($this->jenis_pengajuan) === 'resertifikasi';
    }

    /**
     * Get latest invoice
     */
    public function latestInvoice(): ?Invoice
    {
        return $this->invoices()->latest()->first();
    }

    /**
     * Get latest audit findings
     */
    public function latestAuditFindings(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->auditFindings()->latest()->get();
    }

    public function workflowStage(): int
    {
        return LsproType5Workflow::stageFor($this->status);
    }

    public function workflowStageMeta(): array
    {
        return LsproType5Workflow::stage($this->workflowStage());
    }

    public function workflowStatusLabel(): string
    {
        return LsproType5Workflow::statusLabel($this->status);
    }

    public function workflowProgress(): int
    {
        return LsproType5Workflow::progress($this->status);
    }

    public function workflowNextStatuses(): array
    {
        return LsproType5Workflow::nextStatuses($this->status);
    }

    public function transitionTo(string $status, ?string $notes = null, ?int $actorId = null): void
    {
        LsproType5Workflow::assertTransition($this->status, $status);

        $fromStatus = $this->status;
        $this->status = $status;
        $this->save();

        $this->statusHistories()->create([
            'actor_id' => $actorId,
            'from_status' => $fromStatus,
            'to_status' => $status,
            'notes' => $notes,
        ]);
    }
}
