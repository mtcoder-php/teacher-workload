<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\NotificationService; // YANGI
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'user' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'phone' => $request->user()->phone,
                'avatar' => $request->user()->avatar,
                'avatar_url' => $request->user()->avatar_url,
                'role' => [
                    'id' => $request->user()->role?->id,
                    'name' => $request->user()->role?->name,
                    'display_name' => $request->user()->role?->display_name,
                ],
                'email_verified_at' => $request->user()->email_verified_at,
            ],
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // Eski ma'lumotlarni saqlash
        $oldEmail = $user->email;
        $oldPhone = $user->phone;
        $emailChanged = false;
        
        // Ma'lumotlarni yangilash
        $user->fill($request->validated());

        // Email o'zgarganda verification reset
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
            $emailChanged = true;
        }

        // Avatar yuklash
        if ($request->hasFile('avatar')) {
            // Eski avatarni o'chirish
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Yangi avatarni saqlash
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
            
            // ✅ YANGI: Avatar yangilangani haqida bildirishnoma
            NotificationService::success(
                $user,
                'Profil rasmi yangilandi',
                'Sizning profil rasmingiz muvaffaqiyatli yangilandi'
            );
        }

        $user->save();

        // ✅ YANGI: Email o'zgarganda bildirishnoma
        if ($emailChanged) {
            NotificationService::info(
                $user,
                'Email manzil o\'zgartirildi',
                "Email manzilingiz {$oldEmail} dan {$user->email} ga o'zgartirildi. Yangi emailni tasdiqlang.",
                [
                    'old_email' => $oldEmail,
                    'new_email' => $user->email,
                ]
            );
        }

        // ✅ YANGI: Telefon o'zgarganda bildirishnoma
        if ($oldPhone !== $user->phone && $user->phone) {
            NotificationService::info(
                $user,
                'Telefon raqam yangilandi',
                "Telefon raqamingiz yangilandi: {$user->phone}",
                ['phone' => $user->phone]
            );
        }

        return redirect()->route('profile.edit')
            ->with('success', 'Profil muvaffaqiyatli yangilandi');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.current_password' => 'Joriy parol noto\'g\'ri',
            'password.required' => 'Yangi parol kiritish majburiy',
            'password.min' => 'Parol kamida 8 ta belgidan iborat bo\'lishi kerak',
            'password.confirmed' => 'Parollar mos kelmadi',
        ]);

        $user = $request->user();

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // ✅ YANGI: Parol o'zgartirilgani haqida bildirishnoma
        NotificationService::success(
            $user,
            'Parol o\'zgartirildi',
            'Parolingiz muvaffaqiyatly o\'zgartirildi. Xavfsizlik uchun barcha qurilmalarda tizimga qayta kiring.',
            [
                'changed_at' => now()->format('Y-m-d H:i:s'),
                'ip_address' => $request->ip(),
            ]
        );

        return redirect()->route('profile.edit')
            ->with('success', 'Parol muvaffaqiyatli yangilandi');
    }

    /**
     * Delete avatar
     */
    public function deleteAvatar(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->avatar = null;
            $user->save();

            // ✅ YANGI: Avatar o'chirilgani haqida bildirishnoma
            NotificationService::info(
                $user,
                'Profil rasmi o\'chirildi',
                'Sizning profil rasmingiz o\'chirildi. Yangi rasm yuklashingiz mumkin.'
            );
        }

        return redirect()->route('profile.edit')
            ->with('success', 'Avatar o\'chirildi');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ], [
            'password.required' => 'Parol kiritish majburiy',
            'password.current_password' => 'Parol noto\'g\'ri',
        ]);

        $user = $request->user();
        $userName = $user->name;
        $userEmail = $user->email;

        // ✅ YANGI: Adminlarga xabar (akkaunt o'chirilishidan oldin)
        $admins = \App\Models\User::whereHas('role', function($q) {
            $q->where('name', 'admin');
        })->get();

        foreach ($admins as $admin) {
            NotificationService::warning(
                $admin,
                'Foydalanuvchi akkauntini o\'chirdi',
                "{$userName} ({$userEmail}) o'z akkauntini o'chirdi",
                [
                    'user_name' => $userName,
                    'user_email' => $userEmail,
                    'deleted_at' => now()->format('Y-m-d H:i:s'),
                ]
            );
        }

        // Avatar o'chirish
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Akkaunt o\'chirildi');
    }
}