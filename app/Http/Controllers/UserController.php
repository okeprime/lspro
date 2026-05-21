<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    private function getStats() {
        return [
            'masuk' => DB::table('pengajuan_sertifikasi')->where('status', 'baru')->count(),
            'revisi' => DB::table('pengajuan_sertifikasi')->where('status', 'revisi')->count(),
            'selesai' => DB::table('pengajuan_sertifikasi')->where('status', 'selesai')->count(),
        ];
    }
    public function beranda() {
        $data = $this->getStats();
        $data['total_user'] = User::count();
        return view('beranda', $data);
    }
    public function index() {
        $data = $this->getStats();
        $data['users'] = User::all();
        return view('management_user', $data);
    }
}