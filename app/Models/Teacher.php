<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Workload> $currentWorkloads
 * @property-read int|null $current_workloads_count
 * @property-read \App\Models\Department|null $department
 * @property-read string $employment_type_name
 * @property-read string $full_name
 * @property-read \App\Models\User|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Workload> $workloads
 * @property-read int|null $workloads_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Teacher query()
 * @mixin \Eloquent
 */
class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'department_id',
        'position',
        'academic_degree',
        'academic_title',
        'employment_type',
        'hire_date',
        'birth_date',
        'passport_serial',
        'inn',
        'address',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
            'birth_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * O'qituvchining foydalanuvchi profili
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * O'qituvchining kafedra
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * O'qituvchining yuklamalari
     */
    public function workloads(): HasMany
    {
        return $this->hasMany(Workload::class);
    }

    /**
     * Joriy o'quv yili yuklamalarini olish
     */
    public function currentWorkloads(): HasMany
    {
        return $this->workloads()->whereHas('academicYear', function ($query) {
            $query->where('is_active', true);
        });
    }

    /**
     * O'qituvchining to'liq ismi
     */
    public function getFullNameAttribute(): string
    {
        return $this->user->name;
    }

    /**
     * Bandlik turi nomini olish
     */
    public function getEmploymentTypeNameAttribute(): string
    {
        return match ($this->employment_type) {
            'main_job' => 'Asosiy ish joyi',
            'internal_part_time' => 'O\'rindoshlik (ichki-asosiy)',
            'internal_additional' => 'O\'rindoshlik (ichki-qo\'shimcha)',
            'external_part_time' => 'O\'rindoshlik (tashqi)',
            'hourly' => 'Soatbay',
            default => 'Noma\'lum'
        };
    }



    /**
     * Faqat aktiv o'qituvchilarni filtrlash
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
