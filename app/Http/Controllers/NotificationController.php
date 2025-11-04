<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class NotificationController extends Controller
{
    /**
     * Barcha bildirishnomalar ro'yxati
     */
    public function index(Request $request)
    {
        $query = Notification::where('user_id', Auth::id());

        // Filter bo'yicha
        if ($request->filled('filter')) {
            if ($request->filter === 'unread') {
                $query->where('is_read', false);
            } elseif ($request->filter === 'read') {
                $query->where('is_read', true);
            }
        }

        $notifications = $query->latest()->paginate(20);

        // Statistika
        $stats = [
            'total' => Notification::where('user_id', Auth::id())->count(),
            'unread' => Notification::where('user_id', Auth::id())->where('is_read', false)->count(),
            'read' => Notification::where('user_id', Auth::id())->where('is_read', true)->count(),
        ];

        return Inertia::render('Notifications/Index', [
            'notifications' => $notifications,
            'stats' => $stats,
            'filter' => $request->filter,
        ]);
    }

    /**
     * Bildirishnomani o'qilgan deb belgilash
     */
    public function markAsRead(Notification $notification)
    {
        // Faqat o'z bildirishnomalarini belgilashi mumkin
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->markAsRead();

        return redirect()->back()
            ->with('success', 'Bildirishnoma o\'qilgan deb belgilandi');
    }

    /**
     * Barcha bildirishnomalarni o'qilgan deb belgilash
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return redirect()->back()
            ->with('success', 'Barcha bildirishnomalar o\'qilgan deb belgilandi');
    }

    /**
     * Bildirishnomani o'chirish
     */
    public function destroy(Notification $notification)
    {
        // Faqat o'z bildirishnomalarini o'chirishi mumkin
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->delete();

        return redirect()->back()
            ->with('success', 'Bildirishnoma o\'chirildi');
    }
}