<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())->orderBy('created_at', 'desc')->paginate(20);

        $unreadCount = Notification::where('user_id', Auth::id())->unread()->count();

        return view('admin.notification.index', compact('notifications', 'unreadCount'));
    }

    public function markAsRead(Notification $notification)
    {
        // Pastikan notification milik user yang sedang login
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
            ]);
        }

        return redirect()->back()->with('success', 'Notifikasi telah ditandai sebagai dibaca');
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->unread()
            ->update(['read_at' => now()]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read',
            ]);
        }

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca');
    }

    public function getUnreadCount()
    {
        $count = Notification::where('user_id', Auth::id())->unread()->count();

        return response()->json(['count' => $count]);
    }
}
