<?php

namespace App\Helpers;

use App\Models\Notification;
use App\Models\User;

class NotificationHelper
{
    /**
     * Mengirim notifikasi ke user spesifik (Client)
     */
    public static function sendToUser($userId, $title, $message, $type = 'info', $pengajuanId = null)
    {
        $allowedTypes = ['reminder_survailen', 'dokumen_ditolak', 'audit_scheduled', 'pembayaran_invoice', 'perubahan_status'];
        if (!in_array($type, $allowedTypes)) {
            $type = 'perubahan_status';
        }


        Notification::create([
            'user_id' => $userId,
            'pengajuan_id' => $pengajuanId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'is_read' => false,
        ]);
    }

    /**
     * Mengirim notifikasi ke semua admin dengan sub_role tertentu
     */
    public static function sendToRole($subRole, $title, $message, $type = 'info', $pengajuanId = null)
    {
        $allowedTypes = ['reminder_survailen', 'dokumen_ditolak', 'audit_scheduled', 'pembayaran_invoice', 'perubahan_status'];
        if (!in_array($type, $allowedTypes)) {
            $type = 'perubahan_status';
        }

        $admins = User::where('role', 'admin')->where('sub_role', $subRole)->get();
        
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'pengajuan_id' => $pengajuanId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'is_read' => false,
            ]);
        }
        
        // Selalu kirim tembusan ke Superadmin
        $superadmins = User::where('role', 'superadmin')->get();
        foreach ($superadmins as $super) {
            Notification::create([
                'user_id' => $super->id,
                'pengajuan_id' => $pengajuanId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'is_read' => false,
            ]);
        }
    }
}
