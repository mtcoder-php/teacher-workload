<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'name.required' => 'Ism kiritilishi shart',
            'name.max' => 'Ism 255 belgidan oshmasligi kerak',
            'email.required' => 'Email manzil kiritilishi shart',
            'email.email' => 'Email manzil formati noto\'g\'ri',
            'email.unique' => 'Bu email allaqachon ro\'yxatdan o\'tgan',
            'password.required' => 'Parol kiritilishi shart',
            'password.confirmed' => 'Parol tasdiqlash mos kelmaydi',
        ]);

        // Default rolni olish - 'oqituvchi' yoki birinchi mavjud rol
        $defaultRole = Role::where('name', 'oqituvchi')->first() 
                       ?? Role::first();

        // Agar hech qanday rol topilmasa, xatolik
        if (!$defaultRole) {
            return back()->withErrors([
                'email' => 'Tizimda rollar mavjud emas. Administrator bilan bog\'laning.',
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $defaultRole->id, // Default rol ID
            'is_active' => true,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard'));
    }
}