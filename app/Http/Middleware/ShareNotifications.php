<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ShareNotifications
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Inertia share - lazy loading bilan
        Inertia::share([
            'recentNotifications' => function () {
                return $this->getRecentNotifications();
            },
            'unreadNotificationsCount' => function () {
                return $this->getUnreadCount();
            },
        ]);

        return $next($request);
    }

    /**
     * So'nggi bildirishnomalarni olish
     */
    protected function getRecentNotifications(): array
    {
        if (!Auth::check()) {
            return [];
        }

        try {
            $notifications = Auth::user()
                ->notifications()
                ->latest()
                ->limit(5)
                ->get();

            // Array ga aylantirish
            return $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at->toISOString(),
                    'data' => $notification->data,
                ];
            })->toArray();
        } catch (\Exception $e) {
            \Log::error('Notifications fetch error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * O'qilmagan bildirishnomalar sonini olish
     */
    protected function getUnreadCount(): int
    {
        if (!Auth::check()) {
            return 0;
        }

        try {
            return Auth::user()
                ->notifications()
                ->where('is_read', false)
                ->count();
        } catch (\Exception $e) {
            \Log::error('Unread count error: ' . $e->getMessage());
            return 0;
        }
    }
}