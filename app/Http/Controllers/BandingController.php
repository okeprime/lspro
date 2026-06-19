<?php

namespace App\Http\Controllers;

use App\Models\Banding;
use App\Models\Pengajuan;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BandingController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        // Ambil semua pengajuan milik user untuk ditampilkan di dropdown form banding.
        // Riwayat banding sudah dipindahkan ke halaman Aktivitas (tab Banding/Keluhan).
        $pengajuans = Pengajuan::where('user_id', $userId)->latest()->get();

        return view('banding-laporan', compact('pengajuans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:banding,keluhan,laporan',
            'subjek' => 'required|string|max:255',
            'pesan' => 'required|string',
            'pengajuan_id' => 'nullable|exists:pengajuans,id',
            'file_lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,zip|max:10240',
        ]);

        $banding = new Banding();
        $banding->user_id = Auth::id();
        $banding->pengajuan_id = $request->pengajuan_id;
        $banding->jenis = $request->jenis;
        $banding->subjek = $request->subjek;
        $banding->pesan = $request->pesan;
        $banding->status = 'terkirim';

        if ($request->hasFile('file_lampiran')) {
            $file = $request->file('file_lampiran');
            $filename = time() . '_banding_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(storage_path('app/public/banding_lampiran'), $filename);
            $banding->file_lampiran = $filename;
        }

        $banding->save();

        if ($request->jenis === 'banding') {
            NotificationHelper::sendToRole('layanan', 'Banding Baru', 'Banding baru diajukan oleh ' . Auth::user()->name, 'danger', $request->pengajuan_id);
            NotificationHelper::sendToUser(Auth::id(), 'Banding Terkirim', 'Banding Anda telah diterima dan akan segera diproses.', 'info', $request->pengajuan_id);
        } else {
            NotificationHelper::sendToRole('layanan', 'Keluhan/Laporan Baru', 'Keluhan/Laporan baru diajukan oleh ' . Auth::user()->name, 'warning', $request->pengajuan_id);
            NotificationHelper::sendToUser(Auth::id(), 'Keluhan Terkirim', 'Keluhan/Laporan Anda telah diterima.', 'info', $request->pengajuan_id);
        }

        return redirect()->route('banding.index')->with('success', 'Banding / Laporan Anda berhasil dikirim dan sedang diproses oleh admin.');
    }

    public function adminIndex()
    {
        $bandings = Banding::with(['user', 'pengajuan'])->latest()->get();
        return view('admin.banding', compact('bandings'));
    }

    public function prosesAdmin(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:terkirim,diproses,selesai',
            'catatan_admin' => 'nullable|string',
        ]);

        $banding = Banding::findOrFail($id);
        $banding->update([
            'status' => $request->status,
            'catatan_admin' => $request->catatan_admin,
        ]);

        return redirect()->back()->with('success', 'Status banding / keluhan berhasil diperbarui.');
    }
}
