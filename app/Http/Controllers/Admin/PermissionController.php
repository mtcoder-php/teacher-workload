<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PermissionController extends Controller
{
    /**
     * Ruxsatlar ro'yxati
     */
    public function index(Request $request)
    {
        $query = Permission::withCount('roles');

        // Qidiruv
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('group', 'like', "%{$search}%");
            });
        }

        // Guruh bo'yicha filter
        if ($request->filled('group')) {
            $query->where('group', $request->group);
        }

        $permissions = $query->latest()->paginate(20)->withQueryString();

        // Guruhlar ro'yxati (filter uchun) - null bo'lmaganlarini olish
        $groups = Permission::select('group')
            ->whereNotNull('group') // null bo'lmaganlarini olish
            ->distinct()
            ->orderBy('group')
            ->pluck('group');

        return Inertia::render('Admin/Permissions/Index', [
            'permissions' => $permissions,
            'groups' => $groups,
            'filters' => $request->only(['search', 'group']),
        ]);
    }

    /**
     * Yangi ruxsat yaratish formasi
     */
    public function create()
    {
        // Mavjud guruhlar
        $groups = Permission::select('group')
            ->whereNotNull('group') // null bo'lmaganlarini olish
            ->distinct()
            ->orderBy('group')
            ->pluck('group');

        return Inertia::render('Admin/Permissions/Create', [
            'existingGroups' => $groups,
        ]);
    }

    /**
     * Yangi ruxsat saqlash
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'display_name' => 'required|string|max:255',
            'name' => 'required|string|max:255|unique:permissions,name|regex:/^[a-z0-9._-]+$/',
            'group' => 'required|string|max:50', // max 50 (migration bilan bir xil)
            'description' => 'nullable|string|max:500',
        ], [
            'display_name.required' => 'Ko\'rinadigan nom kiritish majburiy',
            'name.required' => 'Tizim nomi kiritish majburiy',
            'name.unique' => 'Bu nom allaqachon mavjud',
            'name.regex' => 'Nom faqat kichik harflar, raqamlar, nuqta va tire dan iborat bo\'lishi kerak',
            'group.required' => 'Guruhni tanlash majburiy',
        ]);

        DB::beginTransaction();
        try {
            $permission = Permission::create($validated);

            // Adminlarga xabar
            $admins = \App\Models\User::whereHas('role', function($q) {
                $q->where('name', 'admin');
            })->get();

            foreach ($admins as $admin) {
                NotificationService::info(
                    $admin,
                    'Yangi ruxsat yaratildi',
                    "Yangi ruxsat qo'shildi: {$permission->display_name} ({$permission->group})",
                    ['permission_id' => $permission->id]
                );
            }

            DB::commit();

            return redirect()->route('admin.permissions.index')
                ->with('success', 'Ruxsat muvaffaqiyatli yaratildi');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Ruxsatni tahrirlash formasi
     */
    public function edit(Permission $permission)
    {
        // Mavjud guruhlar
        $groups = Permission::select('group')
            ->whereNotNull('group') // null bo'lmaganlarini olish
            ->distinct()
            ->orderBy('group')
            ->pluck('group');

        return Inertia::render('Admin/Permissions/Edit', [
            'permission' => $permission,
            'existingGroups' => $groups,
        ]);
    }

    /**
     * Ruxsatni yangilash
     */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'display_name' => 'required|string|max:255',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions')->ignore($permission->id),
                'regex:/^[a-z0-9._-]+$/'
            ],
            'group' => 'required|string|max:50', // max 50 (migration bilan bir xil)
            'description' => 'nullable|string|max:500',
        ], [
            'display_name.required' => 'Ko\'rinadigan nom kiritish majburiy',
            'name.required' => 'Tizim nomi kiritish majburiy',
            'name.unique' => 'Bu nom allaqachon mavjud',
            'name.regex' => 'Nom faqat kichik harflar, raqamlar, nuqta va tire dan iborat bo\'lishi kerak',
            'group.required' => 'Guruhni tanlash majburiy',
        ]);

        DB::beginTransaction();
        try {
            $permission->update($validated);

            // Ruxsat biriktirilgan rollardagi foydalanuvchilarga xabar
            $roles = $permission->roles;
            foreach ($roles as $role) {
                $users = $role->users;
                foreach ($users as $user) {
                    NotificationService::info(
                        $user,
                        'Ruxsat yangilandi',
                        "Sizning rolingiz uchun ruxsat yangilandi: {$permission->display_name}",
                        ['permission_id' => $permission->id]
                    );
                }
            }

            DB::commit();

            return redirect()->route('admin.permissions.index')
                ->with('success', 'Ruxsat muvaffaqiyatli yangilandi');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Ruxsatni o'chirish
     */
    public function destroy(Permission $permission)
    {
        // Rollarga biriktirilgan ruxsatni o'chirishdan himoya
        if ($permission->roles()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Bu ruxsat rollarga biriktirilgan. Avval rollardan olib tashlang.');
        }

        DB::beginTransaction();
        try {
            $permissionName = $permission->display_name;
            $permission->delete();

            DB::commit();

            return redirect()->route('admin.permissions.index')
                ->with('success', "{$permissionName} o'chirildi");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }
}