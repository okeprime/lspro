<?php

namespace App\Support;

use InvalidArgumentException;

class LsproType5Workflow
{
    public const STAGES = [
        1 => [
            'key' => 'pengajuan',
            'title' => 'Tahap 1: Pengajuan',
            'short_title' => 'Pengajuan',
            'pic' => 'Klien',
            'documents' => ['Form 7.2-1/LS Pro'],
        ],
        2 => [
            'key' => 'billing_1',
            'title' => 'Tahap 2: Billing 1 & Audit Kecukupan',
            'short_title' => 'Billing 1',
            'pic' => 'Administrasi',
            'documents' => ['Billing 1', 'Form 7.2-4/LS Pro'],
        ],
        3 => [
            'key' => 'billing_2',
            'title' => 'Tahap 3: Jadwal & Audit Kesesuaian',
            'short_title' => 'Jadwal & Audit Kesesuaian',
            'pic' => 'Administrasi',
            'documents' => ['Jadwal Audit', 'Billing 3'],
        ],
        4 => [
            'key' => 'audit',
            'title' => 'Tahap 4: Audit Kesesuaian',
            'short_title' => 'Audit Kesesuaian',
            'pic' => 'Tim Audit',
            'documents' => ['Dokumen LKS'],
        ],
        5 => [
            'key' => 'lab_test',
            'title' => 'Tahap 5: Proses Lab & LHP',
            'short_title' => 'Proses Lab & LHP',
            'pic' => 'Tim Lab',
            'documents' => ['Laporan Hasil Uji'],
        ],
        6 => [
            'key' => 'evaluasi',
            'title' => 'Tahap 6: Sidang Komtek / Evaluasi',
            'short_title' => 'Evaluasi',
            'pic' => 'Komtek',
            'documents' => ['Dokumen Komtek', 'Billing 4'],
        ],
        7 => [
            'key' => 'keputusan',
            'title' => 'Tahap 7: Keputusan & Sertifikat',
            'short_title' => 'Keputusan',
            'pic' => 'Direktur',
            'documents' => ['Sertifikat'],
        ],
    ];

    public const STATUSES = [
        'draft',
        'menunggu_ttd',
        'diajukan',
        'perbaikan',
        'billing_1',
        'perjanjian_lampiran',
        'billing_2',
        'audit_kecukupan',
        'menunggu_persetujuan_jadwal',
        'billing_3',
        'proses_audit',
        'tindakan_perbaikan',
        'menunggu_lhp',
        'tinjauan_lhp',
        'billing_4',
        'evaluasi',
        'keputusan',
        'selesai',
        'ditolak',
    ];

    public const STATUS_MAP = [
        'draft' => ['stage' => 1, 'label' => 'Draft'],
        'menunggu_ttd' => ['stage' => 1, 'label' => 'Menunggu Unggah Kop Surat & TTD'],
        'diajukan' => ['stage' => 1, 'label' => 'Menunggu Verifikasi Administrasi'],
        'perbaikan' => ['stage' => 1, 'label' => 'Perbaikan Dokumen', 'correction' => true],
        
        'billing_1' => ['stage' => 2, 'label' => 'Menunggu Pembayaran Billing 1'],
        'perjanjian_lampiran' => ['stage' => 2, 'label' => 'Mengisi Perjanjian & Lampiran'],
        'billing_2' => ['stage' => 2, 'label' => 'Menunggu Pembayaran Billing 2 (Audit Kecukupan)'],
        'audit_kecukupan' => ['stage' => 2, 'label' => 'Audit Kecukupan Dokumen'],
        
        'menunggu_persetujuan_jadwal' => ['stage' => 3, 'label' => 'Penyusunan & Persetujuan Jadwal'],
        'billing_3' => ['stage' => 3, 'label' => 'Menunggu Pembayaran Billing 3 (Audit Kesesuaian & BDLT)'],
        
        'proses_audit' => ['stage' => 4, 'label' => 'Pelaksanaan Audit Lapangan'],
        'tindakan_perbaikan' => ['stage' => 4, 'label' => 'Tindakan Perbaikan (LKS)'],
        
        'menunggu_lhp' => ['stage' => 5, 'label' => 'Menunggu Hasil Uji (LHP)'],
        'tinjauan_lhp' => ['stage' => 5, 'label' => 'Tinjauan Hasil LHP'],
        'billing_4' => ['stage' => 6, 'label' => 'Menunggu Pembayaran Billing 4 (Sidang Komtek)'],
        
        'evaluasi' => ['stage' => 6, 'label' => 'Sidang Komtek / Evaluasi Akhir'],
        
        'keputusan' => ['stage' => 7, 'label' => 'Menunggu Keputusan'],
        'selesai' => ['stage' => 7, 'label' => 'Sertifikat Diterbitkan'],
        'ditolak' => ['stage' => 7, 'label' => 'Ditolak', 'correction' => true],
    ];

    private const LEGACY_STATUS_MAP = [
        'verifikasi_tu' => 'diajukan',
        'lengkap' => 'audit_kecukupan',
        'disetujui_tu' => 'audit_kecukupan',
        'invoice_diterbitkan' => 'billing_1',
        'menunggu_pembayaran' => 'billing_1',
        'pembayaran_terverifikasi' => 'billing_2',
        'perjanjian_sertifikasi' => 'billing_2',
        'penugasan_auditor' => 'menunggu_persetujuan_jadwal',
        'perencanaan_audit' => 'menunggu_persetujuan_jadwal',
        'audit_kesesuaian' => 'proses_audit',
        'proses_evaluator' => 'evaluasi',
        'proses_ppc' => 'menunggu_lhp',
        'proses_lab' => 'menunggu_lhp',
        'evaluasi_hasil' => 'evaluasi',
        'keputusan_sertifikasi' => 'keputusan',
        'sertifikat_terbit' => 'selesai',
        'sertifikat_diserahkan' => 'selesai',
        'rejected' => 'ditolak',
        'evaluasi_724_tu' => 'audit_kecukupan',
        'mengisi_perjanjian' => 'billing_2',
        'menunggu_lampiran' => 'billing_2',
        'proses_evaluasi' => 'menunggu_persetujuan_jadwal',
        'billing_4' => 'billing_4',
    ];

    private const TRANSITIONS = [
        'draft' => ['menunggu_ttd', 'diajukan'],
        'menunggu_ttd' => ['diajukan'],
        'diajukan' => ['billing_1', 'perbaikan'],
        
        'billing_1' => ['perjanjian_lampiran', 'perbaikan'],
        'perjanjian_lampiran' => ['billing_2', 'perbaikan'],
        'billing_2' => ['audit_kecukupan', 'perbaikan'],
        'audit_kecukupan' => ['menunggu_persetujuan_jadwal', 'perbaikan'],
        
        'menunggu_persetujuan_jadwal' => ['billing_3', 'perbaikan'],
        'billing_3' => ['proses_audit', 'perbaikan'],
        
        'proses_audit' => ['tindakan_perbaikan', 'menunggu_lhp'],
        'tindakan_perbaikan' => ['proses_audit', 'menunggu_lhp'],
        
        'menunggu_lhp' => ['tinjauan_lhp', 'billing_4'],
        'tinjauan_lhp' => ['billing_4', 'audit_kecukupan', 'billing_3'],
        'billing_4' => ['evaluasi'],
        
        'evaluasi' => ['keputusan', 'perbaikan'],
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
