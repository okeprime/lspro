<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengajuan;
use App\Models\Banding;
use App\Support\LsproType5Workflow;

class AktivitasController extends Controller
{
    private function getIsInternal()
    {
        $role = trim(strtolower(auth()->user()->role ?? ''));
        return in_array($role, ['superadmin', 'tata_usaha', 'pelayanan', 'audit', 'admin', 'tu']);
    }

    private function getPengajuans($jenis)
    {
        $isInternal = $this->getIsInternal();
        $query = Pengajuan::where('jenis_pengajuan', $jenis)->orderBy('created_at', 'desc');
        
        if ($isInternal) {
            $query->with('user');
        } else {
            $query->where('user_id', auth()->id());
        }

        $pengajuans = $query->get();

        $pengajuans->map(function ($item) {
            $data = $item->data_form;
            if (is_string($data)) {
                $data = json_decode($data, true) ?? [];
            } elseif (is_object($data)) {
                $data = (array) $data;
            }
            $item->parsed_form = is_array($data) ? $data : [];
            return $item;
        });

        return $pengajuans;
    }

    /**
     * Dashboard Aktivitas – halaman pilihan (mirip Pengajuan Index).
     */
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'sertifikasi');
        $isInternal = $this->getIsInternal();
        $list = collect();

        if (in_array($filter, ['sertifikasi', 'resertifikasi', 'survailen'])) {
            $list = $this->getPengajuans($filter);
        } elseif ($filter === 'draft') {
            $query = Pengajuan::where('status', 'draft')->orderBy('created_at', 'desc');
            if ($isInternal) {
                $query->with('user');
            } else {
                $query->where('user_id', auth()->id());
            }
            
            $list = $query->get();
            $list->map(function ($item) {
                $data = $item->data_form;
                if (is_string($data)) {
                    $data = json_decode($data, true) ?? [];
                } elseif (is_object($data)) {
                    $data = (array) $data;
                }
                $item->parsed_form = is_array($data) ? $data : [];
                return $item;
            });
        } elseif ($filter === 'banding') {
            $list = $this->getBandings(['banding']);
        } elseif ($filter === 'keluhan') {
            $list = $this->getBandings(['keluhan', 'laporan']);
        }

        return view('aktivitas.index', compact('filter', 'list', 'isInternal'));
    }

    public function sertifikasi()
    {
        $isInternal = $this->getIsInternal();
        $list = $this->getPengajuans('sertifikasi');
        return view('aktivitas.sertifikasi', [
            'list'       => $list,
            'isInternal' => $isInternal,
        ]);
    }

    public function resertifikasi()
    {
        $isInternal = $this->getIsInternal();
        $list = $this->getPengajuans('resertifikasi');
        return view('aktivitas.resertifikasi', [
            'list'       => $list,
            'isInternal' => $isInternal,
        ]);
    }

    private function getBandings($jenis_array)
    {
        $isInternal = $this->getIsInternal();
        $query = Banding::whereIn('jenis', $jenis_array)->orderBy('created_at', 'desc');
        
        if ($isInternal) {
            $query->with(['user', 'pengajuan']);
        } else {
            $query->where('user_id', auth()->id())->with('pengajuan');
        }

        return $query->get();
    }

    public function banding()
    {
        $isInternal = $this->getIsInternal();
        $list = $this->getBandings(['banding']);
        return view('aktivitas.banding', [
            'list'       => $list,
            'isInternal' => $isInternal,
        ]);
    }

    public function keluhan()
    {
        $isInternal = $this->getIsInternal();
        $list = $this->getBandings(['keluhan', 'laporan']);
        return view('aktivitas.keluhan', [
            'list'       => $list,
            'isInternal' => $isInternal,
        ]);
    }

    /**
     * Menampilkan halaman aktivitas evaluasi pengajuan
     */
    public function evaluasi()
    {
        $user = auth()->user();
        $role = trim(strtolower($user->role));

        if ($role === 'admin' || $role === 'tu' || str_contains($role, 'tu') || str_contains($role, 'admin') || str_contains($role, 'tata')) {
            $pengajuans = Pengajuan::whereIn('status', ['proses_evaluasi', 'proses_audit', 'keputusan'])
                                   ->with('user', 'auditFindings')
                                   ->orderBy('created_at', 'desc')
                                   ->get();
        } else {
            $pengajuans = Pengajuan::where('user_id', $user->id)
                                   ->whereIn('status', ['proses_evaluasi', 'proses_audit', 'keputusan'])
                                   ->with('auditFindings')
                                   ->orderBy('created_at', 'desc')
                                   ->get();
        }

        return view('aktivitas-evaluasi', compact('pengajuans'));
    }

    public function show(Pengajuan $pengajuan)
    {
        $user = auth()->user();
        $role = trim(strtolower($user->role));
        $isInternal = $role === 'admin'
            || $role === 'tu'
            || str_contains($role, 'admin')
            || str_contains($role, 'tata');

        abort_unless($isInternal || $pengajuan->user_id === $user->id, 403);

        $pengajuan->load([
            'user',
            'invoices',
            'auditFindings',
            'statusHistories.actor',
        ]);

        $workflowStages = LsproType5Workflow::STAGES;

        return view('client.workflow-detail', compact('pengajuan', 'workflowStages'));
    }
}
