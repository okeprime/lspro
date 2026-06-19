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
}
