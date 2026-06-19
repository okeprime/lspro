<?php

namespace App\Http\Controllers;

use App\Models\InternalChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        // Default group
        $defaultGroup = 'semua_admin';
        
        // Fetch real team members for the sidebar
        $teamMembers = \App\Models\User::whereIn('role', ['admin', 'superadmin'])
            ->where('is_active', 1)
            ->orderBy('role', 'desc')
            ->get();
            
        return view('admin.chat', ['currentGroup' => $defaultGroup, 'teamMembers' => $teamMembers]);
    }

    public function getMessages($group)
    {
        $messages = InternalChat::with('user')
            ->where('group_name', $group)
            ->latest()
            ->take(50)
            ->get()
            ->reverse()
            ->values();

        // FALLBACK DUMMY DATA FOR CS DEMO
        if ($messages->isEmpty() && str_starts_with($group, 'cs_client_')) {
            $clientId = str_replace('cs_client_', '', $group);
            $clientName = $clientId == 101 ? 'PT Semesta Agro' : 'Klien ' . $clientId;
            
            return response()->json([
                [
                    'user_id' => (int) $clientId,
                    'user' => ['name' => $clientName],
                    'message' => 'Halo Admin, saya mau bertanya terkait proses evaluasi form 7.2-4 apakah sudah selesai?',
                    'created_at' => now()->subMinutes(30)
                ],
                [
                    'user_id' => auth()->id(),
                    'user' => ['name' => auth()->user()->name ?? 'Admin CS'],
                    'message' => 'Halo Bapak/Ibu dari ' . $clientName . '. Proses evaluasi sedang dikerjakan oleh tim teknis kami. Kami akan segera memberi update jika dokumen dinyatakan lengkap.',
                    'created_at' => now()->subMinutes(15)
                ]
            ]);
        }

        return response()->json($messages);
    }

    public function sendMessage(Request $request, $group)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $chat = InternalChat::create([
            'user_id' => Auth::id(),
            'group_name' => $group,
            'message' => $request->message,
        ]);

        return response()->json($chat->load('user'));
    }
}
