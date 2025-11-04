<?php

namespace App\Providers;

use App\Models\Faculty;
use App\Models\Department;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Group;
use App\Models\Workload;
use App\Policies\FacultyPolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\TeacherPolicy;
use App\Policies\SubjectPolicy;
use App\Policies\GroupPolicy;
use App\Policies\WorkloadPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Faculty::class => FacultyPolicy::class,
        Department::class => DepartmentPolicy::class,
        Teacher::class => TeacherPolicy::class,
        Subject::class => SubjectPolicy::class,
        Group::class => GroupPolicy::class,
        Workload::class => WorkloadPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}