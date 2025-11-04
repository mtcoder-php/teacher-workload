<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class RoleController extends Controller
{
    /**
     * Rollar ro'yxati
     */
    public function index(Request $request)
    {
        $query = Role::withCount(['users', 'permissions']);

        // Qidiruv
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $roles = $query->latest()->paginate(20)->withQueryString();

        return Inertia::render('Admin/Roles/Index', [
            'roles' => $roles,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Yangi rol yaratish formasi
     */
    public function create()
    {
        return Inertia::render('Admin/Roles/Create');
    }

    /**
     * Yangi rol saqlash
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'display_name' => 'required|string|max:255',
            'name' => 'required|string|max:255|unique:roles,name|regex:/^[a-z0-9-]+$/',
            'description' => 'nullable|string|max:500',
        ], [
            'display_name.required' => 'Ko\'rinadigan nom kiritish majburiy',
            'name.required' => 'Tizim nomi kiritish majburiy',
            'name.unique' => 'Bu nom allaqachon mavjud',
            'name.regex' => 'Nom faqat kichik harflar, raqamlar va tire (-) dan iborat bo\'lishi kerak',
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create($validated);

            // Adminlarga xabar
            $admins = \App\Models\User::whereHas('role', function($q) {
                $q->where('name', 'admin');
            })->get();

            foreach ($admins as $admin) {
                NotificationService::info(
                    $admin,
                    'Yangi rol yaratildi',
                    "Yangi rol qo'shildi: {$role->display_name}",
                    ['role_id' => $role->id]
                );
            }

            DB::commit();

            return redirect()->route('admin.roles.index')
                ->with('success', 'Rol muvaffaqiyatli yaratildi');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Rolni tahrirlash formasi
     */
    public function edit(Role $role)
    {
        return Inertia::render('Admin/Roles/Edit', [
            'role' => $role,
        ]);
    }

    /**
     * Rolni yangilash
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'display_name' => 'required|string|max:255',
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id), 'regex:/^[a-z0-9-]+$/'],
            'description' => 'nullable|string|max:500',
        ], [
            'display_name.required' => 'Ko\'rinadigan nom kiritish majburiy',
            'name.required' => 'Tizim nomi kiritish majburiy',
            'name.unique' => 'Bu nom allaqachon mavjud',
            'name.regex' => 'Nom faqat kichik harflar, raqamlar va tire (-) dan iborat bo\'lishi kerak',
        ]);

        DB::beginTransaction();
        try {
            $role->update($validated);

            // Rol foydalanuvchilariga xabar
            $users = $role->users;
            foreach ($users as $user) {
                NotificationService::info(
                    $user,
                    'Rol yangilandi',
                    "Sizning rolingiz ({$role->display_name}) yangilandi",
                    ['role_id' => $role->id]
                );
            }

            DB::commit();

            return redirect()->route('admin.roles.index')
                ->with('success', 'Rol muvaffaqiyatli yangilandi');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Rolni o'chirish
     */
    public function destroy(Role $role)
    {
        // Tizim rollarini o'chirishdan himoya
        $protectedRoles = ['admin', 'oqituvchi', 'dekan', 'kafedra-mudiri'];
        
        if (in_array($role->name, $protectedRoles)) {
            return redirect()->back()
                ->with('error', 'Tizim rollarini o\'chirish mumkin emas');
        }

        // Foydalanuvchilar biriktirilgan rolni o'chirishdan himoya
        if ($role->users()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Bu rolga biriktirilgan foydalanuvchilar mavjud. Avval ularni boshqa rolga o\'tkazing.');
        }

        DB::beginTransaction();
        try {
            $roleName = $role->display_name;
            $role->delete();

            DB::commit();

            return redirect()->route('admin.roles.index')
                ->with('success', "{$roleName} o'chirildi");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Rol ruxsatlarini ko'rish
     */
    public function permissions(Role $role)
    {
        $allPermissions = Permission::all()->groupBy('group');
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return Inertia::render('Admin/Roles/Permissions', [
            'role' => $role,
            'allPermissions' => $allPermissions,
            'rolePermissions' => $rolePermissions,
        ]);
    }

    /**
     * Rol ruxsatlarini yangilash
     */
    public function updatePermissions(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::beginTransaction();
        try {
            // Ruxsatlarni yangilash
            $role->permissions()->sync($validated['permissions'] ?? []);

            // Rol foydalanuvchilariga xabar
            $users = $role->users;
            foreach ($users as $user) {
                NotificationService::warning(
                    $user,
                    'Ruxsatlar yangilandi',
                    "Sizning rolingiz ({$role->display_name}) uchun ruxsatlar yangilandi",
                    [
                        'role_id' => $role->id,
                        'permissions_count' => count($validated['permissions'] ?? []),
                    ]
                );
            }

            DB::commit();

            return redirect()->route('admin.roles.index')
                ->with('success', 'Ruxsatlar muvaffaqiyatli yangilandi');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }
}