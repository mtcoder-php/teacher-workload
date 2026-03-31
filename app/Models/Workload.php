<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Workload extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subject_id', 'teacher_id', 'direction_id',
        'academic_year_id', 'department_id',

        // Potok
        'is_potok', 'potok_code', 'workload_type',
        'is_potok_remainder', 'parent_potok_id',

        // 1-semestr (subject jadvalidagi kabi)
        'semester_1_lecture', 'semester_1_practical',
        'semester_1_seminar', 'semester_1_laboratory',
        'semester_1_practice', 'semester_1_exam', 'semester_1_test',

        // 2-semestr (subject jadvalidagi kabi)
        'semester_2_lecture', 'semester_2_practical',
        'semester_2_seminar', 'semester_2_laboratory',
        'semester_2_practice', 'semester_2_exam', 'semester_2_test',

        // Umumiy
        'coursework_hours', 'diploma_hours', 'consultation_hours',

        // Statistika
        'total_students', 'rating', 'total_hours',
        'status', 'approved_by', 'approved_at', 'notes',
        'has_rating',
    ];

    protected $casts = [
        'is_potok' => 'boolean',
        'is_potok_remainder' => 'boolean',
        'total_students' => 'integer',
        'rating' => 'decimal:2',
        'approved_at' => 'datetime',

        // 1-semestr
        'semester_1_lecture' => 'decimal:2',
        'semester_1_practical' => 'decimal:2',
        'semester_1_seminar' => 'decimal:2',
        'semester_1_laboratory' => 'decimal:2',
        'semester_1_practice' => 'decimal:2',
        'semester_1_exam' => 'decimal:2',
        'semester_1_test' => 'decimal:2',

        // 2-semestr
        'semester_2_lecture' => 'decimal:2',
        'semester_2_practical' => 'decimal:2',
        'semester_2_seminar' => 'decimal:2',
        'semester_2_laboratory' => 'decimal:2',
        'semester_2_practice' => 'decimal:2',
        'semester_2_exam' => 'decimal:2',
        'semester_2_test' => 'decimal:2',

        // Umumiy
        'coursework_hours' => 'decimal:2',
        'diploma_hours' => 'decimal:2',
        'consultation_hours' => 'decimal:2',
        'total_hours' => 'decimal:2',
        'has_rating' => 'boolean',
    ];

    protected $appends = ['calculated_total_hours', 'status_label'];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'workload_groups');
    }

    public function direction(): BelongsTo
    {
        return $this->belongsTo(Direction::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function parentPotok(): BelongsTo
    {
        return $this->belongsTo(Workload::class, 'parent_potok_id');
    }

    public function remainders(): HasMany
    {
        return $this->hasMany(Workload::class, 'parent_potok_id');
    }

    public function history(): HasMany
    {
        return $this->hasMany(WorkloadHistory::class);
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    /**
     * Jami soatlar (hisoblangan)
     */
    public function getCalculatedTotalHoursAttribute(): float
    {
        $fields = [
            'semester_1_lecture', 'semester_1_practical',
            'semester_1_seminar', 'semester_1_laboratory',
            'semester_1_practice', 'semester_1_exam', 'semester_1_test',
            'semester_2_lecture', 'semester_2_practical',
            'semester_2_seminar', 'semester_2_laboratory',
            'semester_2_practice', 'semester_2_exam', 'semester_2_test',
            'coursework_hours', 'diploma_hours', 'consultation_hours'
        ];

        return (float) array_sum(array_map(fn($field) => floatval($this->$field ?? 0), $fields));
    }

    /**
     * Status nomi
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Qoralama',
            'pending' => 'Tekshiruvda',
            'confirmed' => 'Tasdiqlangan',
            'completed' => 'Tugatilgan',
            default => 'Noma\'lum'
        };
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeByDepartment($query, int $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeByTeacher($query, int $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeByAcademicYear($query, int $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    public function scopePotok($query)
    {
        return $query->where('is_potok', true);
    }

    public function scopeNonPotok($query)
    {
        return $query->where('is_potok', false);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeConfirmed($query)
    {
        return $query->whereIn('status', ['confirmed', 'completed']);
    }

    // ==========================================
    // METHODS
    // ==========================================

    /**
     * Reytingni hisoblash
     */
    public function calculateRating(): void
    {
        if ($this->total_students > 0) {
            $this->rating = round($this->total_students / 2, 2);
            $this->saveQuietly();
        }
    }

    /**
     * Jami soatlarni hisoblash va saqlash
     */
    public function calculateTotalHours(): void
    {
        $this->total_hours = $this->calculated_total_hours;
        $this->saveQuietly();
    }

    /**
     * Tahrirlash mumkinmi?
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, ['draft', 'pending']);
    }

    /**
     * O'chirish mumkinmi?
     */
    public function canBeDeleted(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Tasdiqlash mumkinmi?
     */
    public function canBeApproved(): bool
    {
        return in_array($this->status, ['draft', 'pending']);
    }

    /**
     * Yuklama turini aniqlash
     */
    public function getWorkloadType(): string
    {
        if ($this->is_potok) {
            return 'potok';
        }

        $hasLecture = ($this->semester_1_lecture + $this->semester_2_lecture) > 0;
        $hasOthers = (
                $this->semester_1_practical + $this->semester_1_laboratory +
                $this->semester_1_seminar + $this->semester_2_practical +
                $this->semester_2_laboratory + $this->semester_2_seminar
            ) > 0;

        if ($hasLecture && $hasOthers) {
            return 'full';
        } elseif ($hasLecture) {
            return 'lecture_only';
        } elseif ($hasOthers) {
            return 'practice_only';
        }

        return 'other';
    }

    /**
     * Semestr bo'yicha soatlar
     */
    public function getSemesterHours(int $semester): array
    {
        $prefix = "semester_{$semester}_";

        return [
            'lecture' => floatval($this->{$prefix . 'lecture'} ?? 0),
            'practical' => floatval($this->{$prefix . 'practical'} ?? 0),
            'laboratory' => floatval($this->{$prefix . 'laboratory'} ?? 0),
            'seminar' => floatval($this->{$prefix . 'seminar'} ?? 0),
            'practice' => floatval($this->{$prefix . 'practice'} ?? 0),
            'exam' => floatval($this->{$prefix . 'exam'} ?? 0),
            'test' => floatval($this->{$prefix . 'test'} ?? 0),
        ];
    }

    /**
     * Soat turlari bo'yicha statistika
     */
    public function getHoursByType(): array
    {
        return [
            'lecture' => $this->semester_1_lecture + $this->semester_2_lecture,
            'practical' => $this->semester_1_practical + $this->semester_2_practical,
            'laboratory' => $this->semester_1_laboratory + $this->semester_2_laboratory,
            'seminar' => $this->semester_1_seminar + $this->semester_2_seminar,
            'practice' => $this->semester_1_practice + $this->semester_2_practice,
            'exam' => $this->semester_1_exam + $this->semester_2_exam,
            'test' => $this->semester_1_test + $this->semester_2_test,
            'coursework' => $this->coursework_hours,
            'diploma' => $this->diploma_hours,
            'consultation' => $this->consultation_hours,
        ];
    }

    // ==========================================
    // BOOT
    // ==========================================

    protected static function boot()
    {
        parent::boot();

        // Yaratilganda
        static::creating(function ($workload) {
            if ($workload->total_students > 0 && !$workload->rating) {
                $workload->rating = round($workload->total_students / 2, 2);
            }
            $workload->total_hours = $workload->calculated_total_hours;
        });

        // Yangilanganda
        static::updating(function ($workload) {
            if ($workload->isDirty('total_students')) {
                $workload->rating = round($workload->total_students / 2, 2);
            }

            // Soatlar o'zgarganda jami soatni yangilash
            $hourFields = [
                'semester_1_lecture', 'semester_1_practical',
                'semester_1_seminar', 'semester_1_laboratory',
                'semester_1_practice', 'semester_1_exam', 'semester_1_test',
                'semester_2_lecture', 'semester_2_practical',
                'semester_2_seminar', 'semester_2_laboratory',
                'semester_2_practice', 'semester_2_exam', 'semester_2_test',
                'coursework_hours', 'diploma_hours', 'consultation_hours',
            ];

            if (collect($hourFields)->some(fn($field) => $workload->isDirty($field))) {
                $workload->total_hours = $workload->calculated_total_hours;
            }
        });
    }
}
