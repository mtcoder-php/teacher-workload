<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Foydalanuvchilar ro'yxati
     */
    public function index(Request $request)
    {
        $query = User::with('role');

        // Qidiruv
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Rol bo'yicha filter
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // Status bo'yicha filter
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'true');
        }

        $users = $query->latest()->paginate(20)->withQueryString();
        $roles = Role::all();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'roles' => $roles,
            'filters' => $request->only(['search', 'role_id', 'is_active']),
        ]);
    }

    /**
     * Yangi foydalanuvchi yaratish formasi
     */
    public function create()
    {
        $roles = Role::all();

        return Inertia::render('Admin/Users/Create', [
            'roles' => $roles,
        ]);
    }

    /**
     * Yangi foydalanuvchi saqlash
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Ism kiritish majburiy',
            'email.required' => 'Email kiritish majburiy',
            'email.unique' => 'Bu email allaqachon mavjud',
            'password.required' => 'Parol kiritish majburiy',
            'password.min' => 'Parol kamida 8 ta belgidan iborat bo\'lishi kerak',
            'password.confirmed' => 'Parollar mos kelmadi',
            'role_id.required' => 'Rolni tanlash majburiy',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password']),
                'role_id' => $validated['role_id'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            // Foydalanuvchiga xush kelibsiz bildirishnomasi
            NotificationService::success(
                $user,
                'Xush kelibsiz!',
                'Sizning akkauntingiz yaratildi. Tizimga kirish uchun email va parolingizdan foydalaning.',
                ['created_by' => auth()->user()->name]
            );

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'Foydalanuvchi muvaffaqiyatli yaratildi');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Foydalanuvchini tahrirlash formasi
     */
    public function edit(User $user)
    {
        $roles = Role::all();

        return Inertia::render('Admin/Users/Edit', [
            'user' => $user->load('role'),
            'roles' => $roles,
        ]);
    }

    /**
     * Foydalanuvchini yangilash
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Ism kiritish majburiy',
            'email.required' => 'Email kiritish majburiy',
            'email.unique' => 'Bu email allaqachon mavjud',
            'password.min' => 'Parol kamida 8 ta belgidan iborat bo\'lishi kerak',
            'password.confirmed' => 'Parollar mos kelmadi',
            'role_id.required' => 'Rolni tanlash majburiy',
        ]);

        DB::beginTransaction();
        try {
            $oldData = [
                'role_id' => $user->role_id,
                'is_active' => $user->is_active,
            ];

            $data = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'role_id' => $validated['role_id'],
                'is_active' => $validated['is_active'] ?? true,
            ];

            // Parol o'zgartirilgan bo'lsa
            if (!empty($validated['password'])) {
                $data['password'] = Hash::make($validated['password']);
                
                NotificationService::warning(
                    $user,
                    'Parol o\'zgartirildi',
                    'Admin tomonidan parolingiz yangilandi.',
                    ['changed_by' => auth()->user()->name]
                );
            }

            $user->update($data);

            // Rol o'zgargan bo'lsa
            if ($oldData['role_id'] !== $user->role_id) {
                NotificationService::info(
                    $user,
                    'Rol o\'zgartirildi',
                    'Sizning rolingiz yangilandi: ' . $user->role->display_name,
                    ['changed_by' => auth()->user()->name]
                );
            }

            // Status o'zgargan bo'lsa
            if ($oldData['is_active'] !== $user->is_active) {
                $status = $user->is_active ? 'faollashtirildi' : 'bloklandi';
                NotificationService::warning(
                    $user,
                    'Akkaunt holati',
                    "Akkauntingiz {$status}",
                    ['changed_by' => auth()->user()->name]
                );
            }

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'Foydalanuvchi muvaffaqiyatli yangilandi');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Foydalanuvchini o'chirish
     */
    public function destroy(User $user)
    {
        // O'zini o'chirishni oldini olish
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'O\'z akkauntingizni o\'chira olmaysiz');
        }

        DB::beginTransaction();
        try {
            $userName = $user->name;
            $user->delete();

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', "{$userName} o'chirildi");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Foydalanuvchi statusini o'zgartirish
     */
    public function toggleStatus(User $user)
    {
        // O'zini bloklashni oldini olish
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'O\'z akkauntingizni bloklay olmaysiz');
        }

        DB::beginTransaction();
        try {
            $user->is_active = !$user->is_active;
            $user->save();

            $status = $user->is_active ? 'faollashtirildi' : 'bloklandi';

            NotificationService::warning(
                $user,
                'Akkaunt holati o\'zgartirildi',
                "Akkauntingiz {$status}",
                ['changed_by' => auth()->user()->name]
            );

            DB::commit();

            return redirect()->back()
                ->with('success', "Foydalanuvchi {$status}");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }
}