<?php

namespace App\Support;

use InvalidArgumentException;

class LsproType5Workflow
{
    public const STAGES = [
        1 => [
            'key' => 'pengajuan',
            'title' => 'Pengajuan',
            'short_title' => 'Pengajuan',
            'pic' => 'Klien',
            'documents' => ['Form 7.2-1/LS Pro'],
        ],
        2 => [
            'key' => 'verifikasi_tu',
            'title' => 'Verifikasi TU',
            'short_title' => 'Verifikasi TU',
            'pic' => 'Tata Usaha',
            'documents' => ['Form 7.2-4/LS Pro'],
        ],
        3 => [
            'key' => 'perjanjian',
            'title' => 'Perjanjian Sertifikasi',
            'short_title' => 'Perjanjian',
            'pic' => 'Tata Usaha dan Klien',
            'documents' => ['Form 7.2-3/LS Pro'],
        ],
        4 => [
            'key' => 'billing',
            'title' => 'Billing',
            'short_title' => 'Billing',
            'pic' => 'Tata Usaha',
            'documents' => ['Invoice atau tagihan sertifikasi'],
        ],
        5 => [
            'key' => 'evaluasi',
            'title' => 'Evaluasi',
            'short_title' => 'Evaluasi',
            'pic' => 'Tim Evaluasi',
            'documents' => ['Dokumen evaluasi sertifikasi'],
        ],
        6 => [
            'key' => 'audit',
            'title' => 'Audit',
            'short_title' => 'Audit',
            'pic' => 'Tim Audit',
            'documents' => ['Dokumen audit dan pengambilan contoh'],
        ],
        7 => [
            'key' => 'keputusan',
            'title' => 'Keputusan',
            'short_title' => 'Keputusan',
            'pic' => 'Komisi Teknis dan Ketua LS Pro',
            'documents' => ['Keputusan sertifikasi'],
        ],
        8 => [
            'key' => 'sppt_sni',
            'title' => 'SPPT SNI',
            'short_title' => 'SPPT SNI',
            'pic' => 'LS Pro',
            'documents' => ['SPPT SNI'],
        ],
    ];

    public const STATUSES = [
        'draft',
        'menunggu_ttd',
        'diajukan',
        'verifikasi_tu',
        'perbaikan',
        'perjanjian',
        'billing',
        'proses_evaluasi',
        'proses_audit',
        'keputusan',
        'selesai',
        'ditolak',
    ];

    private const STATUS_MAP = [
        'draft' => ['stage' => 1, 'label' => 'Draft'],
        'diajukan' => ['stage' => 2, 'label' => 'Verifikasi Awal TU'],
        'menunggu_lampiran' => ['stage' => 3, 'label' => 'Menunggu Kelengkapan Berkas'],
        'perbaikan' => ['stage' => 2, 'label' => 'Perbaikan Dokumen', 'correction' => true],
        'evaluasi_724_tu' => ['stage' => 4, 'label' => 'Evaluasi Kelengkapan (TU)'],
        'evaluasi_724_audit' => ['stage' => 4, 'label' => 'Evaluasi Kebenaran (Audit)'],
        'menunggu_ttd' => ['stage' => 5, 'label' => 'Menunggu TTD Dokumen'],
        'billing' => ['stage' => 6, 'label' => 'Pembayaran (Billing)'],
        'proses_evaluasi' => ['stage' => 7, 'label' => 'Proses Evaluasi'],
        'proses_audit' => ['stage' => 8, 'label' => 'Proses Audit'],
        'keputusan' => ['stage' => 9, 'label' => 'Menunggu Keputusan'],
        'selesai' => ['stage' => 10, 'label' => 'Selesai - SPPT SNI Terbit'],
        'ditolak' => ['stage' => 9, 'label' => 'Ditolak', 'rejected' => true],
    ];

    private const LEGACY_STATUS_MAP = [
        'lengkap' => 'perjanjian',
        'disetujui_tu' => 'perjanjian',
        'invoice_diterbitkan' => 'billing',
        'menunggu_pembayaran' => 'billing',
        'pembayaran_terverifikasi' => 'proses_evaluasi',
        'perjanjian_sertifikasi' => 'perjanjian',
        'penugasan_auditor' => 'proses_evaluasi',
        'audit_kecukupan' => 'proses_audit',
        'perencanaan_audit' => 'proses_audit',
        'audit_kesesuaian' => 'proses_audit',
        'proses_evaluator' => 'proses_evaluasi',
        'evaluasi_hasil' => 'keputusan',
        'keputusan_sertifikasi' => 'keputusan',
        'sertifikat_terbit' => 'selesai',
        'sertifikat_diserahkan' => 'selesai',
        'rejected' => 'ditolak',
    ];

    private const TRANSITIONS = [
        'draft' => ['diajukan'],
        'diajukan' => ['menunggu_lampiran', 'perbaikan'],
        'perbaikan' => ['diajukan', 'menunggu_lampiran'],
        'menunggu_lampiran' => ['evaluasi_724_tu'],
        'evaluasi_724_tu' => ['evaluasi_724_audit', 'perbaikan'],
        'evaluasi_724_audit' => ['menunggu_ttd', 'perbaikan'],
        'menunggu_ttd' => ['billing'],
        'billing' => ['proses_evaluasi'],
        'proses_evaluasi' => ['proses_audit'],
        'proses_audit' => ['keputusan'],
        'keputusan' => ['selesai', 'ditolak'],
        'selesai' => [],
        'ditolak' => [],
    ];

    public static function stageFor(?string $status): int
    {
        return self::statusMeta($status)['stage'];
    }

    public static function statusLabel(?string $status): string
    {
        return self::statusMeta($status)['label'];
    }

    public static function stage(?int $stage): array
    {
        return self::STAGES[$stage] ?? self::STAGES[1];
    }

    public static function progress(?string $status): int
    {
        return (int) round((self::stageFor($status) / count(self::STAGES)) * 100);
    }

    public static function isCorrection(?string $status): bool
    {
        return (bool) (self::statusMeta($status)['correction'] ?? false);
    }

    public static function isRejected(?string $status): bool
    {
        return (bool) (self::statusMeta($status)['rejected'] ?? false);
    }

    public static function nextStatuses(?string $status): array
    {
        $normalized = self::normalize($status);
        $statuses = self::TRANSITIONS[$normalized] ?? [];

        return array_map(
            fn (string $nextStatus) => [
                'value' => $nextStatus,
                'label' => self::statusLabel($nextStatus),
                'stage' => self::stageFor($nextStatus),
            ],
            $statuses
        );
    }

    public static function canTransition(?string $fromStatus, string $toStatus): bool
    {
        return in_array(self::normalize($toStatus), self::TRANSITIONS[self::normalize($fromStatus)] ?? [], true);
    }

    public static function assertTransition(?string $fromStatus, string $toStatus): void
    {
        if (!self::canTransition($fromStatus, $toStatus)) {
            throw new InvalidArgumentException('Perpindahan tahap LSPro tidak valid.');
        }
    }

    public static function normalize(?string $status): string
    {
        $normalized = strtolower(trim((string) $status));

        return self::LEGACY_STATUS_MAP[$normalized] ?? $normalized;
    }

    private static function statusMeta(?string $status): array
    {
        $normalized = self::normalize($status);

        return self::STATUS_MAP[$normalized] ?? [
            'stage' => 1,
            'label' => $status ? ucwords(str_replace('_', ' ', $status)) : 'Belum dimulai',
        ];
    }
}
