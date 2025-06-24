<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())->orderBy('created_at', 'desc')->paginate(20);

        $unreadCount = Notification::where('user_id', Auth::id())->unread()->count();

        return view('admin.notification.index', compact('notifications', 'unreadCount'));
    }

    public function markAsRead($id)
    {
        try {
            $notification = Notification::where('id', $id)->where('user_id', Auth::id())->first();

            if (!$notification) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Notifikasi tidak ditemukan',
                    ],
                    404,
                );
            }

            // Check if already read
            if ($notification->isRead()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notifikasi sudah dibaca sebelumnya',
                ]);
            }

            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi berhasil ditandai sebagai dibaca',
            ]);
        } catch (\Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menandai notifikasi sebagai dibaca',
                ],
                500,
            );
        }
    }

    public function markAllAsRead()
    {
        try {
            $updatedCount = Notification::where('user_id', Auth::id())
                ->unread()
                ->update(['read_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => "Berhasil menandai {$updatedCount} notifikasi sebagai dibaca",
                'updated_count' => $updatedCount,
            ]);
        } catch (\Exception $e) {
            Log::error('Error marking all notifications as read: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menandai semua notifikasi sebagai dibaca',
                ],
                500,
            );
        }
    }

    public function getUnreadCount()
    {
        try {
            $count = Notification::where('user_id', Auth::id())->unread()->count();

            return response()->json([
                'success' => true,
                'count' => $count,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting unread count: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'count' => 0,
            ]);
        }
    }
}
