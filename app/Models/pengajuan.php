<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'file_permohonan',
        'status',
        'data_form',
        'catatan_evaluasi',
    ];

    /**
     * Casting tipe data kolom saat berinteraksi dengan database.
     * Kolom 'data_form' bertipe JSON/Text akan dikonversi menjadi Array PHP secara otomatis.
     */
    protected $casts = [
        'data_form' => 'array',
    ];

    /**
     * Hubungan Relasi Balik ke Model User (Pemilik Berkas Pengajuan)
     * * Menghubungkan kolom 'user_id' di tabel pengajuans ke kolom 'id' di tabel users.
     * FUNGSI INI WAJIB ADA agar perintah Eager Loading `with('user')` pada AdminController dapat bekerja.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}