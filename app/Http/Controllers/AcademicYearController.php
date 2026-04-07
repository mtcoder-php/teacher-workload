<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAcademicYearRequest;
use App\Http\Requests\UpdateAcademicYearRequest;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicYearController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::latest()
            ->paginate(15);

        return inertia('AcademicYears/Index', [
            'academicYears' => $academicYears
        ]);
    }

    public function create()
    {
        return inertia('AcademicYears/Create');
    }

    public function store(StoreAcademicYearRequest $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();

            if ($validated['is_active'] ?? false) {
                AcademicYear::query()->update(['is_active' => false]);
            }

            AcademicYear::create($validated);

            DB::commit();

            return redirect()->route('academic-years.index')
                ->with('success', 'O\'quv yili muvaffaqiyatli qo\'shildi');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    public function show(AcademicYear $academicYear)
    {
        return inertia('AcademicYears/Show', [
            'academicYear'    => $academicYear,
            'workloads_count' => \App\Models\Workload::where('academic_year_id', $academicYear->id)->count(),
            'confirmed_count' => \App\Models\Workload::where('academic_year_id', $academicYear->id)
                ->where('status', 'confirmed')->count(),
            'teachers_count'  => \App\Models\Workload::where('academic_year_id', $academicYear->id)
                ->distinct('teacher_id')->count('teacher_id'),
        ]);
    }

    public function edit(AcademicYear $academicYear)
    {
        return inertia('AcademicYears/Edit', [
            'academicYear' => $academicYear
        ]);
    }

    public function update(UpdateAcademicYearRequest $request, AcademicYear $academicYear)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();

            if ($validated['is_active'] ?? false) {
                AcademicYear::where('id', '!=', $academicYear->id)
                    ->update(['is_active' => false]);
            }

            $academicYear->update($validated);

            DB::commit();

            return redirect()->route('academic-years.index')
                ->with('success', 'O\'quv yili muvaffaqiyatli yangilandi');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    public function destroy(AcademicYear $academicYear)
    {
        if ($academicYear->is_active) {
            return redirect()->route('academic-years.index')
                ->with('error', 'Joriy o\'quv yilini o\'chirish mumkin emas');
        }

        if ($academicYear->semesters()->count() > 0) {
            return redirect()->route('academic-years.index')
                ->with('error', 'Bu o\'quv yilida semestrlar mavjud. Avval ularni o\'chiring');
        }

        DB::beginTransaction();
        try {
            $academicYear->delete();

            DB::commit();

            return redirect()->route('academic-years.index')
                ->with('success', 'O\'quv yili muvaffaqiyatli o\'chirildi');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    public function setCurrent(AcademicYear $academicYear)
    {
        DB::beginTransaction();
        try {
            // ✅ TO'G'RI
            $academicYear->activate();

            DB::commit();

            return redirect()->route('academic-years.index')
                ->with('success', 'Joriy o\'quv yili o\'rnatildi');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }
}
