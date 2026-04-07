<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Direction;
use App\Models\Faculty;
use App\Models\Group;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Workload;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin())          return $this->adminDashboard();
        if ($user->isDean())           return $this->deanDashboard();
        if ($user->isDepartmentHead()) return $this->departmentHeadDashboard();
        if ($user->isTeacher())        return $this->teacherDashboard();

        return Inertia::render('Dashboard', [
            'stats' => [], 'recentActivities' => [], 'role' => 'default',
        ]);
    }

    // ─── Admin ────────────────────────────────────────────────────────────────
    protected function adminDashboard()
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $yearId     = $activeYear?->id;

        $wq = fn() => Workload::when($yearId, fn($q) => $q->where('academic_year_id', $yearId));

        $stats = [
            'faculties_count'    => Faculty::where('is_active', true)->count(),
            'departments_count'  => Department::where('is_active', true)->count(),
            'teachers_count'     => Teacher::where('is_active', true)->count(),
            'groups_count'       => Group::where('is_active', true)->count(),
            'subjects_count'     => Subject::where('is_active', true)->count(),
            'workloads_total'    => $wq()->count(),
            'workloads_draft'    => $wq()->where('status', 'draft')->count(),
            'workloads_pending'  => $wq()->where('status', 'pending')->count(),
            'workloads_confirmed'=> $wq()->where('status', 'confirmed')->count(),
        ];

        $recent = Workload::with(['teacher.user:id,name', 'subject:id,name'])
            ->when($yearId, fn($q) => $q->where('academic_year_id', $yearId))
            ->latest()->take(8)->get()
            ->map(fn($w) => [
                'id'          => $w->id,
                'teacher'     => $w->teacher?->user?->name ?? '—',
                'subject'     => $w->subject?->name ?? '—',
                'status'      => $w->status,
                'total_hours' => round($w->total_hours, 1),
                'time'        => $w->created_at->diffForHumans(),
            ]);

        return Inertia::render('Dashboard', [
            'stats'            => $stats,
            'recentActivities' => $recent,
            'activeYear'       => $activeYear?->name,
            'role'             => 'admin',
        ]);
    }

    // ─── Dekan ────────────────────────────────────────────────────────────────
    protected function deanDashboard()
    {
        $user    = Auth::user();
        $faculty = Faculty::where('dean_id', $user->id)->first();
        $yearId  = AcademicYear::where('is_active', true)->value('id');

        if (!$faculty) {
            return Inertia::render('Dashboard', [
                'stats' => [], 'recentActivities' => [], 'role' => 'dekan',
            ]);
        }

        $deptIds = Department::where('faculty_id', $faculty->id)->pluck('id');
        $wq = fn() => Workload::whereIn('department_id', $deptIds)
            ->when($yearId, fn($q) => $q->where('academic_year_id', $yearId));

        $stats = [
            'departments_count'  => $deptIds->count(),
            'teachers_count'     => Teacher::whereIn('department_id', $deptIds)->where('is_active', true)->count(),
            'subjects_count'     => Subject::whereIn('department_id', $deptIds)->where('is_active', true)->count(),
            'workloads_total'    => $wq()->count(),
            'workloads_pending'  => $wq()->where('status', 'pending')->count(),
            'workloads_confirmed'=> $wq()->where('status', 'confirmed')->count(),
        ];

        $recent = Workload::with(['teacher.user:id,name', 'subject:id,name'])
            ->whereIn('department_id', $deptIds)
            ->when($yearId, fn($q) => $q->where('academic_year_id', $yearId))
            ->latest()->take(8)->get()
            ->map(fn($w) => [
                'id'          => $w->id,
                'teacher'     => $w->teacher?->user?->name ?? '—',
                'subject'     => $w->subject?->name ?? '—',
                'status'      => $w->status,
                'total_hours' => round($w->total_hours, 1),
                'time'        => $w->created_at->diffForHumans(),
            ]);

        return Inertia::render('Dashboard', [
            'stats'            => $stats,
            'recentActivities' => $recent,
            'activeYear'       => AcademicYear::where('is_active', true)->value('name'),
            'role'             => 'dekan',
            'context'          => $faculty->name,
        ]);
    }

    // ─── Kafedra mudiri ───────────────────────────────────────────────────────
    protected function departmentHeadDashboard()
    {
        $user       = Auth::user();
        $department = Department::where('head_id', $user->id)->first();
        $yearId     = AcademicYear::where('is_active', true)->value('id');

        if (!$department) {
            return Inertia::render('Dashboard', [
                'stats' => [], 'recentActivities' => [], 'role' => 'kafedra_mudiri',
            ]);
        }

        $wq = fn() => Workload::where('department_id', $department->id)
            ->when($yearId, fn($q) => $q->where('academic_year_id', $yearId));

        $stats = [
            'teachers_count'     => Teacher::where('department_id', $department->id)->where('is_active', true)->count(),
            'subjects_count'     => Subject::where('department_id', $department->id)->where('is_active', true)->count(),
            'directions_count'   => Direction::where('department_id', $department->id)->count(),
            'workloads_total'    => $wq()->count(),
            'workloads_draft'    => $wq()->where('status', 'draft')->count(),
            'workloads_pending'  => $wq()->where('status', 'pending')->count(),
            'workloads_confirmed'=> $wq()->where('status', 'confirmed')->count(),
        ];

        $recent = Workload::with(['teacher.user:id,name', 'subject:id,name'])
            ->where('department_id', $department->id)
            ->when($yearId, fn($q) => $q->where('academic_year_id', $yearId))
            ->latest()->take(8)->get()
            ->map(fn($w) => [
                'id'          => $w->id,
                'teacher'     => $w->teacher?->user?->name ?? '—',
                'subject'     => $w->subject?->name ?? '—',
                'status'      => $w->status,
                'total_hours' => round($w->total_hours, 1),
                'time'        => $w->created_at->diffForHumans(),
            ]);

        return Inertia::render('Dashboard', [
            'stats'            => $stats,
            'recentActivities' => $recent,
            'activeYear'       => AcademicYear::where('is_active', true)->value('name'),
            'role'             => 'kafedra_mudiri',
            'context'          => $department->name,
        ]);
    }

    // ─── O'qituvchi ───────────────────────────────────────────────────────────
    protected function teacherDashboard()
    {
        $user    = Auth::user();
        $teacher = $user->teacher;
        $yearId  = AcademicYear::where('is_active', true)->value('id');

        if (!$teacher) {
            return Inertia::render('Dashboard', [
                'stats' => [], 'recentActivities' => [], 'role' => 'oqituvchi',
            ]);
        }

        $wq = fn() => Workload::where('teacher_id', $teacher->id)
            ->when($yearId, fn($q) => $q->where('academic_year_id', $yearId));

        $stats = [
            'workloads_total'    => $wq()->count(),
            'workloads_draft'    => $wq()->where('status', 'draft')->count(),
            'workloads_pending'  => $wq()->where('status', 'pending')->count(),
            'workloads_confirmed'=> $wq()->where('status', 'confirmed')->count(),
            'total_hours'        => round((float) $wq()->sum('total_hours'), 1),
        ];

        $recent = Workload::with(['subject:id,name'])
            ->where('teacher_id', $teacher->id)
            ->when($yearId, fn($q) => $q->where('academic_year_id', $yearId))
            ->latest()->take(8)->get()
            ->map(fn($w) => [
                'id'          => $w->id,
                'teacher'     => $user->name,
                'subject'     => $w->subject?->name ?? '—',
                'status'      => $w->status,
                'total_hours' => round($w->total_hours, 1),
                'time'        => $w->created_at->diffForHumans(),
            ]);

        return Inertia::render('Dashboard', [
            'stats'            => $stats,
            'recentActivities' => $recent,
            'activeYear'       => AcademicYear::where('is_active', true)->value('name'),
            'role'             => 'oqituvchi',
            'context'          => $teacher->department?->name ?? '',
        ]);
    }
}
