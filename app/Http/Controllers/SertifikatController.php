<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Support\LsproType5Workflow;

class SertifikatController extends Controller
{
    public function index()
    {
        // Get all approved/finished applications that have resulted in a certificate
        $sertifikats = Pengajuan::where('user_id', Auth::id())
            ->where('status', 'selesai')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('client.sertifikat', compact('sertifikats'));
    }

    public function cetak($id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        
        // Ensure the user owns this pengajuan or is admin
        if ($pengajuan->user_id !== Auth::id() && Auth::user()->role !== 'Admin') {
            abort(403, 'Unauthorized access.');
        }

        return view('client.cetak_sertifikat', compact('pengajuan'));
    }
}
