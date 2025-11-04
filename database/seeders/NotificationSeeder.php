<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('Foydalanuvchilar topilmadi! Avval UserSeeder ni ishga tushiring.');
            return;
        }

        foreach ($users as $user) {
            // Har bir foydalanuvchiga 5-10 ta bildirishnoma
            $count = rand(5, 10);

            for ($i = 0; $i < $count; $i++) {
                $this->createRandomNotification($user);
            }
        }

        $this->command->info('Bildirishnomalar muvaffaqiyatli yaratildi!');
    }

    private function createRandomNotification(User $user): void
    {
        $types = ['info', 'success', 'warning', 'error', 'workload', 'user'];
        $type = $types[array_rand($types)];

        $notifications = [
            'info' => [
                ['title' => 'Yangi funksiya', 'message' => 'Tizimda yangi hisobot funksiyasi qo\'shildi'],
                ['title' => 'Texnik ishlar', 'message' => 'Bugun kechqurun 22:00 da texnik ishlar rejalashtirilgan'],
                ['title' => 'Yangilik', 'message' => 'Kafedrada yangi kompyuter xonasi ochildi'],
                ['title' => 'E\'lon', 'message' => 'Ertaga kafedra yig\'ilishi bo\'ladi'],
            ],
            'success' => [
                ['title' => 'Yuklama tasdiqlandi', 'message' => 'Sizning yuklamangiz muvaffaqiyatli tasdiqlandi'],
                ['title' => 'Profil yangilandi', 'message' => 'Profilingiz muvaffaqiyatli yangilandi'],
                ['title' => 'Hisobot yuborildi', 'message' => 'Oylik hisobotingiz muvaffaqiyatli yuborildi'],
                ['title' => 'Amal bajarildi', 'message' => 'So\'ralgan amal muvaffaqiyatli bajarildi'],
            ],
            'warning' => [
                ['title' => 'Muddati yaqinlashmoqda', 'message' => 'Yuklamani to\'ldirish muddati 3 kundan keyin tugaydi'],
                ['title' => 'To\'ldirilmagan ma\'lumot', 'message' => 'Profilingizda to\'ldirilmagan ma\'lumotlar mavjud'],
                ['title' => 'Eslatma', 'message' => 'Ertaga soat 14:00 da yig\'ilish bo\'ladi'],
                ['title' => 'Diqqat', 'message' => 'Yuklamangizni tekshirish kerak'],
            ],
            'error' => [
                ['title' => 'Xatolik yuz berdi', 'message' => 'Hisobotni yuklashda xatolik yuz berdi'],
                ['title' => 'Yuklama rad etildi', 'message' => 'Sizning yuklamangiz rad etildi. Qaytadan ko\'rib chiqing'],
                ['title' => 'Muammo', 'message' => 'Tizimda muammo aniqlandi. Admin bilan bog\'laning'],
                ['title' => 'Ruxsat yo\'q', 'message' => 'Sizda bu amalni bajarish uchun ruxsat yo\'q'],
            ],
            'workload' => [
                ['title' => 'Yangi yuklama', 'message' => 'Sizga Matematika fani bo\'yicha yangi yuklama tayinlandi'],
                ['title' => 'Yuklama o\'zgartirildi', 'message' => 'Fizika fani bo\'yicha yuklamangiz yangilandi'],
                ['title' => 'Yuklama bekor qilindi', 'message' => 'Kimyo fani bo\'yicha yuklamangiz bekor qilindi'],
                ['title' => 'Yuklama tasdiqlandi', 'message' => 'Ingliz tili fani bo\'yicha yuklamangiz tasdiqlandi'],
            ],
            'user' => [
                ['title' => 'Yangi foydalanuvchi', 'message' => 'Kafedrada yangi o\'qituvchi ro\'yxatdan o\'tdi'],
                ['title' => 'Profil o\'zgarishi', 'message' => 'Sizning profilingiz admin tomonidan yangilandi'],
                ['title' => 'Parol o\'zgartirildi', 'message' => 'Parolingiz muvaffaqiyatli o\'zgartirildi'],
                ['title' => 'Ruxsatlar yangilandi', 'message' => 'Sizning ruxsatlaringiz yangilandi'],
            ],
        ];

        $randomNotification = $notifications[$type][array_rand($notifications[$type])];

        // O'qilgan/o'qilmaganligini random qilish (70% o'qilgan)
        $isRead = rand(1, 100) <= 70;

        Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $randomNotification['title'],
            'message' => $randomNotification['message'],
            'data' => [
                'source' => 'system',
                'random_id' => rand(1, 1000),
            ],
            'is_read' => $isRead,
            'read_at' => $isRead ? now()->subDays(rand(0, 30)) : null,
            'created_at' => now()->subDays(rand(0, 30)),
        ]);
    }
}