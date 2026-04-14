<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Direction;
use App\Models\AcademicYear;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $query = Group::with(['direction.department']);

        // Search by name or code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Filter by direction
        if ($request->filled('direction_id')) {
            $query->where('direction_id', $request->direction_id);
        }

        // Filter by course (1-6)
        if ($request->filled('course')) {
            $query->where('course', $request->course);
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Filter by education type
        if ($request->filled('education_type')) {
            $query->where('education_type', $request->education_type);
        }

        // ✅ YANGI FILTER - education_language
        if ($request->filled('education_language')) {
            $query->where('education_language', $request->education_language);
        }

        $groups = $query->latest()->paginate(10)->withQueryString();

        return inertia('Groups/Index', [
            'groups' => $groups,
            'directions' => Direction::where('is_active', true)
                ->with('department')
                ->orderBy('name')
                ->get(),
            'filters' => $request->only([
                'search',
                'direction_id',
                'course',
                'is_active',
                'education_type',
                'education_language' // ✅ QOSHILDI
            ]),
        ]);
    }

    public function create()
    {
        $currentYear = AcademicYear::where('is_active', true)->first();

        return inertia('Groups/Create', [
            'directions'   => Direction::where('is_active', true)
                ->with('department')
                ->orderBy('name')
                ->get(),
            'currentYear'  => $currentYear,
        ]);
    }

    public function store(StoreGroupRequest $request)
    {
        try {
            Group::create($request->validated());

            return redirect()->route('groups.index')
                ->with('success', 'Guruh muvaffaqiyatli yaratildi! ✅');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Guruh yaratishda xatolik: ' . $e->getMessage());
        }
    }

    public function show(Group $group)
    {
        $group->load(['direction:id,name,department_id', 'direction.department:id,name']);

        return inertia('Groups/Show', [
            'group'          => $group,
            'workloads_count'=> $group->workloads()->count(),
        ]);
    }

    public function edit(Group $group)
    {
        $group->load(['direction.department']);

        return inertia('Groups/Edit', [
            'group' => $group,
            'directions' => Direction::where('is_active', true)
                ->with('department')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(UpdateGroupRequest $request, Group $group)
    {
        try {
            $group->update($request->validated());

            return redirect()->route('groups.index')
                ->with('success', 'Guruh ma\'lumotlari yangilandi! ✅');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Guruh yangilashda xatolik: ' . $e->getMessage());
        }
    }

    public function destroy(Group $group)
    {
        if ($group->workloads()->exists()) {
            return back()->with('error', 'Bu guruhda yuklamalar mavjud. Avval yuklamalarni o\'chiring! ❌');
        }

        try {
            $group->delete();

            return redirect()->route('groups.index')
                ->with('success', 'Guruh o\'chirildi! ✅');
        } catch (\Exception $e) {
            return back()->with('error', 'Guruh o\'chirishda xatolik: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Group $group)
    {
        try {
            $group->update(['is_active' => !$group->is_active]);

            return back()->with('success', $group->is_active
                ? 'Guruh faollashtirildi! ✅'
                : 'Guruh nofaol qilindi! ⚠️'
            );
        } catch (\Exception $e) {
            return back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }
}
