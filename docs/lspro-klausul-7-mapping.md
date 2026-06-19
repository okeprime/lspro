# Pemetaan Alur LSPro Klausul 7

## Sumber

- `Persiapan Assement.xlsx`, sheet `Klausul 7`
- Diagram `Alur Sertifikasi Tipe 5`
- `Mock Up App_edt.pdf` sebagai referensi pola status dan riwayat permohonan

PDF mockup membahas layanan laboratorium dan informasi, bukan proses sertifikasi
LSPro. Karena itu, proses sampel pada PDF tidak dipakai sebagai aturan bisnis.

## Alur Inti Sertifikasi Tipe 5

| Tahap | Proses | Dokumen utama |
| --- | --- | --- |
| 01 | Pengajuan dari pemohon | Form 7.2-1/LS Pro |
| 02 | Penerimaan dan pemeriksaan berkas | Form 7.2-4/LS Pro |
| 03 | Penerbitan invoice | Invoice/tagihan sertifikasi |
| 04 | Perjanjian sertifikasi | Form 7.2-3/LS Pro |
| 05 | Penugasan auditor dan PPC | Form 7.2-7/LS Pro, surat tugas |
| 06 | Audit kecukupan | Form 7.4-3/LS Pro |
| 07 | Perencanaan audit dan sampel | Form 7.4/LS Pro, Form 7.4-1/LS Pro |
| 08 | Audit kesesuaian dan pengambilan contoh | Form 7.4-2, 7.4-11, 7.4-12 |
| 09 | Evaluasi dan keputusan sertifikasi | Form 7.4-4, 7.6-1, 7.6-2 |
| 10 | Penerbitan dan penyerahan sertifikat | Form 7.7, 7.7-1, 7.8-1 |

## Modul Lanjutan dari Excel

Bagian berikut merupakan proses setelah atau di luar alur sertifikasi awal dan
perlu dibangun sebagai modul terpisah:

- DP 7.9: survailen
- DP 7.10: evaluasi ulang dan perubahan persyaratan
- DP 7.11: pengurangan, pembekuan, dan pencabutan sertifikasi
- DP 7.13: keluhan dan banding

## Implementasi Saat Ini

- State machine 10 tahap berada di `app/Support/LsproType5Workflow.php`.
- Setiap perubahan status divalidasi agar tidak melompati tahap.
- Invoice dibuat ketika proses masuk tahap 03.
- Pembayaran invoice ditandai lunas ketika diverifikasi.
- Riwayat status disimpan dalam `pengajuan_status_histories`.
- Klien dapat melihat tahap, PIC, dokumen, progres, dan riwayat permohonan.
- Panel admin hanya menawarkan tahap lanjutan yang valid.
