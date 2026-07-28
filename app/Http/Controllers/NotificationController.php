<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->latest()
            ->get();
            
        $unreadCount = $notifications->where('is_read', false)->count();

        // Auto mark as read when viewed
        if ($unreadCount > 0) {
            Notification::where('user_id', Auth::id())
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return view('client.notifikasi', compact('notifications', 'unreadCount'));
    }

    public function destroy($id)
    {
        $notif = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notif->delete();
        
        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    public function markAllRead(Request $request)
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return back();
    }
}
