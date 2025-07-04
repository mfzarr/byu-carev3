<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;
use App\Services\NotifikasiService;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function getNotifications()
    {
        $notifications = NotifikasiService::getUserNotifications(Auth::id());
        $unreadCount = NotifikasiService::getUnreadCount(Auth::id());

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    public function markAsRead(Request $request)
    {
        $notifikasi_id = $request->id;
        NotifikasiService::markAsRead($notifikasi_id);

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        NotifikasiService::markAllAsRead(Auth::id());

        return response()->json(['success' => true]);
    }

    public function getAdminNotifications()
    {
        // Get notifications for admin users (you might want to adjust this query)
        $notifications = Notifikasi::whereHas('user', function ($query) {
            $query->where('role', 'admin');
        })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $unreadCount = Notifikasi::whereHas('user', function ($query) {
            $query->where('role', 'admin');
        })
            ->where('is_read', false)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }
}
