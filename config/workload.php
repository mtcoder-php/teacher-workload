<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Yuklama Sozlamalari
    |--------------------------------------------------------------------------
    */

    // O'qituvchining maksimal yillik soati
    'max_teacher_hours' => env('MAX_TEACHER_HOURS', 900),

    // Potok uchun minimal guruhlar soni
    'min_potok_groups' => env('MIN_POTOK_GROUPS', 2),

    // Potok uchun maksimal guruhlar soni
    'max_potok_groups' => env('MAX_POTOK_GROUPS', 10),

    // Reyting koeffitsienti
    'rating_coefficient' => env('RATING_COEFFICIENT', 0.5), // talabalar_soni / 2

    // Statuslar
    'statuses' => [
        'draft' => 'Qoralama',
        'pending' => 'Tekshiruvda',
        'confirmed' => 'Tasdiqlangan',
        'completed' => 'Tugatilgan',
    ],

    // Soat turlari
    'hour_types' => [
        'lecture' => 'Ma\'ruza',
        'practical' => 'Amaliy',
        'laboratory' => 'Laboratoriya',
        'seminar' => 'Seminar',
        'practice' => 'Amaliyot',
        'exam' => 'Imtihon',
        'test' => 'Test',
        'coursework' => 'Kurs ishi',
        'diploma' => 'Diplom',
        'consultation' => 'Konsultatsiya',
    ],

    // Ta'lim shakllari
    'education_forms' => [
        'full_time' => 'Kunduzgi',
        'evening' => 'Kechki',
        'correspondence' => 'Sirtqi',
        'distance' => 'Masofaviy',
    ],

    // Paginatsiya
    'pagination' => [
        'per_page' => 20,
        'options' => [10, 20, 50, 100],
    ],

    // Export sozlamalari
    'export' => [
        'enabled' => true,
        'formats' => ['pdf', 'excel', 'csv'],
    ],

    // Notifications
    'notifications' => [
        'enabled' => true,
        'channels' => ['database', 'mail'],
    ],
];
