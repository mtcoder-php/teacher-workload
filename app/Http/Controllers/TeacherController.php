<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Models\Department;
use App\Models\Teacher;
use App\Models\User;
use App\Services\NotificationService; // YANGI
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // YANGI
use Inertia\Inertia;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $query = Teacher::with(['user', 'department.faculty']);

        // Filter bo'yicha
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->employment_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $teachers = $query->latest()->paginate(20)->withQueryString();
        $departments = Department::where('is_active', true)->get(['id', 'name']);

        return Inertia::render('Teachers/Index', [
            'teachers' => $teachers,
            'departments' => $departments,
            'filters' => $request->only(['search', 'department_id', 'employment_type'])
        ]);
    }

    public function create()
    {
        $departments = Department::where('is_active', true)
            ->with('faculty:id,name')
            ->get(['id', 'name', 'faculty_id']);

        return Inertia::render('Teachers/Create', [
            'departments' => $departments
        ]);
    }

    public function store(StoreTeacherRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // O'qituvchi roli
            $teacherRole = \App\Models\Role::where('name', 'oqituvchi')->first();

            // User yaratish
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'] ?? null,
                'role_id' => $teacherRole->id,
                'is_active' => true,
            ]);

            // Teacher profili yaratish
            $teacher = Teacher::create([
                'user_id' => $user->id,
                'department_id' => $validated['department_id'],
                'position' => $validated['position'] ?? null,
                'academic_degree' => $validated['academic_degree'] ?? null,
                'academic_title' => $validated['academic_title'] ?? null,
                'employment_type' => $validated['employment_type'],
                'hire_date' => $validated['hire_date'] ?? null,
                'birth_date' => $validated['birth_date'] ?? null,
                'passport_serial' => $validated['passport_serial'] ?? null,
                'inn' => $validated['inn'] ?? null,
                'address' => $validated['address'] ?? null,
            ]);

            // ✅ YANGI: O'qituvchiga xush kelibsiz bildirishnomasi
            NotificationService::success(
                $user,
                'Xush kelibsiz!',
                "Siz {$teacher->department->name} kafedrasida o'qituvchi sifatida ro'yxatdan o'tdingiz. Tizimga kirish uchun email va parolingizdan foydalaning.",
                [
                    'department' => $teacher->department->name,
                    'position' => $validated['position'] ?? 'O\'qituvchi',
                ]
            );

            // ✅ YANGI: Kafedra mudiri va adminlarga xabar
            $departmentHead = $teacher->department->head ?? null;
            if ($departmentHead && $departmentHead->user) {
                NotificationService::info(
                    $departmentHead->user,
                    'Yangi o\'qituvchi',
                    "{$user->name} kafedrangizga yangi o'qituvchi sifatida qo'shildi",
                    ['teacher_id' => $teacher->id]
                );
            }

            DB::commit();

            return redirect()->route('teachers.index')
                ->with('success', 'O\'qituvchi muvaffaqiyatli qo\'shildi');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    public function show(Teacher $teacher)
    {
        $teacher->load(['user', 'department.faculty', 'workloads.subject', 'workloads.group', 'workloads.semester']);

        // Statistika
        $stats = [
            'total_workloads' => $teacher->workloads()->count(),
            'current_workloads' => $teacher->currentWorkloads()->count(),
            'total_hours' => $teacher->currentWorkloads()->sum('total_hours'),
        ];

        return Inertia::render('Teachers/Show', [
            'teacher' => $teacher,
            'stats' => $stats
        ]);
    }

    public function edit(Teacher $teacher)
    {
        $teacher->load('user');
        
        $departments = Department::where('is_active', true)
            ->with('faculty:id,name')
            ->get(['id', 'name', 'faculty_id']);

        return Inertia::render('Teachers/Edit', [
            'teacher' => $teacher,
            'departments' => $departments
        ]);
    }

    public function update(UpdateTeacherRequest $request, Teacher $teacher)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // Eski ma'lumotlarni saqlash
            $oldDepartment = $teacher->department;
            $wasActive = $teacher->is_active;

            // User yangilash
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
            ];

            // Agar yangi parol kiritilgan bo'lsa
            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
                
                // ✅ YANGI: Parol o'zgartirilganligi haqida bildirishnoma
                NotificationService::info(
                    $teacher->user,
                    'Parol o\'zgartirildi',
                    'Sizning parolingiz admin tomonidan yangilandi. Yangi parol bilan tizimga kiring.',
                    ['changed_by' => auth()->user()->name]
                );
            }

            $teacher->user->update($userData);

            // Teacher profili yangilash
            $teacher->update([
                'department_id' => $validated['department_id'],
                'position' => $validated['position'] ?? null,
                'academic_degree' => $validated['academic_degree'] ?? null,
                'academic_title' => $validated['academic_title'] ?? null,
                'employment_type' => $validated['employment_type'],
                'hire_date' => $validated['hire_date'] ?? null,
                'birth_date' => $validated['birth_date'] ?? null,
                'passport_serial' => $validated['passport_serial'] ?? null,
                'inn' => $validated['inn'] ?? null,
                'address' => $validated['address'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            $teacher->refresh();

            // ✅ YANGI: Kafedra o'zgargan bo'lsa
            if ($oldDepartment->id !== $teacher->department_id) {
                NotificationService::info(
                    $teacher->user,
                    'Kafedra o\'zgartirildi',
                    "Siz {$oldDepartment->name} kafedrasidan {$teacher->department->name} kafedrasiga o'tkazdingiz",
                    [
                        'old_department' => $oldDepartment->name,
                        'new_department' => $teacher->department->name,
                    ]
                );
            }

            // ✅ YANGI: Faollik holati o'zgargan bo'lsa
            $isActive = $validated['is_active'] ?? true;
            if ($wasActive && !$isActive) {
                NotificationService::warning(
                    $teacher->user,
                    'Akkaunt bloklandi',
                    'Sizning akkauntingiz vaqtincha bloklandi. Tafsilotlar uchun admin bilan bog\'laning.',
                    ['blocked_by' => auth()->user()->name]
                );
            } elseif (!$wasActive && $isActive) {
                NotificationService::success(
                    $teacher->user,
                    'Akkaunt faollashtirildi',
                    'Sizning akkauntingiz qayta faollashtirildi. Endi tizimdan foydalanishingiz mumkin.',
                    ['activated_by' => auth()->user()->name]
                );
            }

            DB::commit();

            return redirect()->route('teachers.index')
                ->with('success', 'O\'qituvchi muvaffaqiyatli yangilandi');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    public function destroy(Teacher $teacher)
    {
        DB::beginTransaction();
        try {
            $userName = $teacher->user->name;
            $departmentName = $teacher->department->name;

            // ✅ YANGI: O'qituvchiga bildirishnoma (o'chirilishidan oldin)
            NotificationService::warning(
                $teacher->user,
                'Akkaunt o\'chirilmoqda',
                'Sizning akkauntingiz tizimdan o\'chirilmoqda. Tafsilotlar uchun admin bilan bog\'laning.',
                ['deleted_by' => auth()->user()->name]
            );

            // Cascade deletion will handle teacher record
            $teacher->user->delete(); 

            // ✅ YANGI: Kafedra mudiriga xabar
            $departmentHead = Department::find($teacher->department_id)->head ?? null;
            if ($departmentHead && $departmentHead->user) {
                NotificationService::info(
                    $departmentHead->user,
                    'O\'qituvchi o\'chirildi',
                    "{$userName} {$departmentName} kafedrasidan o'chirildi",
                    [
                        'teacher_name' => $userName,
                        'deleted_by' => auth()->user()->name,
                    ]
                );
            }

            DB::commit();

            return redirect()->route('teachers.index')
                ->with('success', 'O\'qituvchi muvaffaqiyatli o\'chirildi');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }
}