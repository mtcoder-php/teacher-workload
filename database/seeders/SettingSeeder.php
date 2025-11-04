<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ============================================
            // UMUMIY (General)
            // ============================================
            [
                'key' => 'app_name',
                'value' => 'O\'quv Yuki Tizimi',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Ilova nomi',
                'description' => 'Tizimning asosiy nomi',
                'is_public' => true,
            ],
            [
                'key' => 'app_name_short',
                'value' => 'OYT',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Qisqa nom',
                'description' => 'Ilova qisqa nomi',
                'is_public' => true,
            ],
            [
                'key' => 'app_logo',
                'value' => '/images/logo.png',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Logo',
                'description' => 'Tizim logosi',
                'is_public' => true,
            ],
            [
                'key' => 'university_name',
                'value' => 'Toshkent Davlat Iqtisodiyot Universiteti',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Universitet nomi',
                'description' => 'To\'liq universitet nomi',
                'is_public' => true,
            ],
            [
                'key' => 'contact_email',
                'value' => 'admin@tdiu.uz',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Email',
                'description' => 'Aloqa email manzili',
                'is_public' => true,
            ],
            [
                'key' => 'contact_phone',
                'value' => '+998 71 239 11 11',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Telefon',
                'description' => 'Aloqa telefon raqami',
                'is_public' => true,
            ],
            [
                'key' => 'address',
                'value' => 'Toshkent sh., Islom Karimov ko\'chasi, 49',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Manzil',
                'description' => 'Universitet manzili',
                'is_public' => true,
            ],

            // ============================================
            // O'QUV (Academic)
            // ============================================
            [
                'key' => 'academic_year_current',
                'value' => '2024-2025',
                'type' => 'text',
                'group' => 'academic',
                'label' => 'Joriy o\'quv yili',
                'description' => 'Hozirgi o\'quv yili',
                'is_public' => true,
            ],
            [
                'key' => 'semester_current',
                'value' => '1',
                'type' => 'number',
                'group' => 'academic',
                'label' => 'Joriy semestr',
                'description' => '1 yoki 2',
                'is_public' => true,
            ],
            [
                'key' => 'semester_1_start',
                'value' => '2024-09-01',
                'type' => 'text',
                'group' => 'academic',
                'label' => '1-semestr boshlanishi',
                'description' => 'Birinchi semestr boshlanish sanasi',
                'is_public' => false,
            ],
            [
                'key' => 'semester_1_end',
                'value' => '2025-01-31',
                'type' => 'text',
                'group' => 'academic',
                'label' => '1-semestr tugashi',
                'description' => 'Birinchi semestr tugash sanasi',
                'is_public' => false,
            ],
            [
                'key' => 'semester_2_start',
                'value' => '2025-02-01',
                'type' => 'text',
                'group' => 'academic',
                'label' => '2-semestr boshlanishi',
                'description' => 'Ikkinchi semestr boshlanish sanasi',
                'is_public' => false,
            ],
            [
                'key' => 'semester_2_end',
                'value' => '2025-06-30',
                'type' => 'text',
                'group' => 'academic',
                'label' => '2-semestr tugashi',
                'description' => 'Ikkinchi semestr tugash sanasi',
                'is_public' => false,
            ],

            // ============================================
            // YUKLAMA (Workload)
            // ============================================
            [
                'key' => 'workload_max_hours_per_week',
                'value' => '18',
                'type' => 'number',
                'group' => 'workload',
                'label' => 'Maksimal haftalik soat',
                'description' => 'O\'qituvchining haftasiga maksimal soat yuklamasi',
                'is_public' => false,
            ],
            [
                'key' => 'workload_min_hours_per_week',
                'value' => '6',
                'type' => 'number',
                'group' => 'workload',
                'label' => 'Minimal haftalik soat',
                'description' => 'O\'qituvchining haftasiga minimal soat yuklamasi',
                'is_public' => false,
            ],
            [
                'key' => 'workload_lecture_coefficient',
                'value' => '1.0',
                'type' => 'number',
                'group' => 'workload',
                'label' => 'Ma\'ruza koeffitsienti',
                'description' => 'Ma\'ruza soatlari uchun koeffitsient',
                'is_public' => false,
            ],
            [
                'key' => 'workload_practice_coefficient',
                'value' => '1.0',
                'type' => 'number',
                'group' => 'workload',
                'label' => 'Amaliy koeffitsienti',
                'description' => 'Amaliy soatlar uchun koeffitsient',
                'is_public' => false,
            ],

            // ============================================
            // XAVFSIZLIK (Security)
            // ============================================
            [
                'key' => 'security_session_lifetime',
                'value' => '120',
                'type' => 'number',
                'group' => 'security',
                'label' => 'Sessiya muddati (daqiqa)',
                'description' => 'Foydalanuvchi sessiyasi muddati',
                'is_public' => false,
            ],
            [
                'key' => 'security_password_min_length',
                'value' => '8',
                'type' => 'number',
                'group' => 'security',
                'label' => 'Parol minimal uzunligi',
                'description' => 'Parolning minimal belgilar soni',
                'is_public' => false,
            ],
            [
                'key' => 'security_require_strong_password',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'security',
                'label' => 'Kuchli parol',
                'description' => 'Parolda harflar, raqamlar va belgilar bo\'lishi shart',
                'is_public' => false,
            ],

            // ============================================
            // EMAIL
            // ============================================
            [
                'key' => 'email_notifications_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'email',
                'label' => 'Email xabarnomalar',
                'description' => 'Email orqali xabar yuborish',
                'is_public' => false,
            ],
            [
                'key' => 'email_from_name',
                'value' => 'O\'quv Yuki Tizimi',
                'type' => 'text',
                'group' => 'email',
                'label' => 'Yuboruvchi nomi',
                'description' => 'Email yuboruvchi ko\'rinadigan nomi',
                'is_public' => false,
            ],

            // ============================================
            // TIZIM (System)
            // ============================================
            [
                'key' => 'system_maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'system',
                'label' => 'Texnik ishlar rejimi',
                'description' => 'Tizimni vaqtinchalik o\'chirish',
                'is_public' => true,
            ],
            [
                'key' => 'system_maintenance_message',
                'value' => 'Tizimda texnik ishlar olib borilmoqda.',
                'type' => 'text',
                'group' => 'system',
                'label' => 'Texnik ishlar xabari',
                'description' => 'Foydalanuvchilarga ko\'rsatiladigan xabar',
                'is_public' => true,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('Settings seeded successfully!');
    }
}