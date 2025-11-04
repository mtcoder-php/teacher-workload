<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Foydalanuvchiga bildirishnoma yuborish
     */
    public static function send(
        User $user,
        string $type,
        string $title,
        string $message,
        ?array $data = null
    ): Notification {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Info bildirishnoma
     */
    public static function info(User $user, string $title, string $message, ?array $data = null): Notification
    {
        return self::send($user, 'info', $title, $message, $data);
    }

    /**
     * Success bildirishnoma
     */
    public static function success(User $user, string $title, string $message, ?array $data = null): Notification
    {
        return self::send($user, 'success', $title, $message, $data);
    }

    /**
     * Warning bildirishnoma
     */
    public static function warning(User $user, string $title, string $message, ?array $data = null): Notification
    {
        return self::send($user, 'warning', $title, $message, $data);
    }

    /**
     * Error bildirishnoma
     */
    public static function error(User $user, string $title, string $message, ?array $data = null): Notification
    {
        return self::send($user, 'error', $title, $message, $data);
    }

    /**
     * Yuklama bildirishnomasi
     */
    public static function workload(User $user, string $title, string $message, ?array $data = null): Notification
    {
        return self::send($user, 'workload', $title, $message, $data);
    }

    /**
     * Bir nechta foydalanuvchilarga bildirishnoma
     */
    public static function sendToMany(
        $users,
        string $type,
        string $title,
        string $message,
        ?array $data = null
    ): void {
        foreach ($users as $user) {
            self::send($user, $type, $title, $message, $data);
        }
    }

    /**
     * Barcha foydalanuvchilarga bildirishnoma
     */
    public static function sendToAll(
        string $type,
        string $title,
        string $message,
        ?array $data = null
    ): void {
        $users = User::all();
        self::sendToMany($users, $type, $title, $message, $data);
    }
}

// Foydalanish namunasi:
/*
use App\Services\NotificationService;

// Bitta foydalanuvchiga
NotificationService::success(
    $user,
    'Yuklama tayinlandi',
    'Sizga yangi yuklama tayinlandi: Matematika - 101-guruh',
    ['workload_id' => 123]
);

// Info bildirishnoma
NotificationService::info(
    $user,
    'Yangilik',
    'Tizimda yangi funksiya qo\'shildi'
);

// Warning bildirishnoma
NotificationService::warning(
    $user,
    'Eslatma',
    'Yuklamangizni to\'ldirish muddati yaqinlashmoqda'
);

// Bir nechta foydalanuvchiga
$teachers = User::where('role', 'teacher')->get();
NotificationService::sendToMany(
    $teachers,
    'info',
    'Muhim xabar',
    'Ertaga yig\'ilish bo\'ladi'
);

// Barcha foydalanuvchilarga
NotificationService::sendToAll(
    'warning',
    'Texnik ishlar',
    'Tizim 20:00 da texnik ishlar uchun to\'xtatiladi'
);
*/