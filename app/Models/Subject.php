<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string $code
 * @property int $department_id
 * @property int|null $direction_id
 * @property int $course_level
 * @property int $credit_hours
 * @property float $semester_1_lecture
 * @property float $semester_1_practical
 * @property float $semester_1_laboratory
 * @property float $semester_1_seminar
 * @property float $semester_1_practice
 * @property float $semester_1_exam
 * @property float $semester_1_test
 * @property float $semester_2_lecture
 * @property float $semester_2_practical
 * @property float $semester_2_laboratory
 * @property float $semester_2_seminar
 * @property float $semester_2_practice
 * @property float $semester_2_exam
 * @property float $semester_2_test
 * @property float $coursework_hours
 * @property float $diploma_hours
 * @property float $consultation_hours
 * @property float $total_hours
 * @property string $subject_type
 * @property string|null $education_form
 * @property bool $can_be_potok
 * @property int $min_groups_for_potok
 * @property string|null $semester_1_control
 * @property string|null $semester_2_control
 * @property string|null $description
 * @property bool $is_active
 * @property-read \App\Models\Department $department
 * @property-read \App\Models\Direction|null $direction
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Workload> $workloads
 */
class Subject extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'department_id',
        'direction_id',
        'course_level',
        'credit_hours',
        
        // 1-semestr
        'semester_1_lecture',
        'semester_1_practical',
        'semester_1_laboratory',
        'semester_1_seminar',
        'semester_1_practice',
        'semester_1_exam',
        'semester_1_test',
        
        // 2-semestr
        'semester_2_lecture',
        'semester_2_practical',
        'semester_2_laboratory',
        'semester_2_seminar',
        'semester_2_practice',
        'semester_2_exam',
        'semester_2_test',
        
        // Qo'shimcha
        'coursework_hours',
        'diploma_hours',
        'consultation_hours',
        
        // Fan turlari
        'subject_type',
        'education_form',
        'can_be_potok',
        'min_groups_for_potok',
        
        // Nazorat
        'semester_1_control',
        'semester_2_control',
        
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'course_level' => 'integer',
            'credit_hours' => 'integer',
            
            // 1-semestr soatlari
            'semester_1_lecture' => 'decimal:2',
            'semester_1_practical' => 'decimal:2',
            'semester_1_laboratory' => 'decimal:2',
            'semester_1_seminar' => 'decimal:2',
            'semester_1_practice' => 'decimal:2',
            'semester_1_exam' => 'decimal:2',
            'semester_1_test' => 'decimal:2',
            
            // 2-semestr soatlari
            'semester_2_lecture' => 'decimal:2',
            'semester_2_practical' => 'decimal:2',
            'semester_2_laboratory' => 'decimal:2',
            'semester_2_seminar' => 'decimal:2',
            'semester_2_practice' => 'decimal:2',
            'semester_2_exam' => 'decimal:2',
            'semester_2_test' => 'decimal:2',
            
            // Qo'shimcha soatlar
            'coursework_hours' => 'decimal:2',
            'diploma_hours' => 'decimal:2',
            'consultation_hours' => 'decimal:2',
            'total_hours' => 'decimal:2',
            
            'can_be_potok' => 'boolean',
            'min_groups_for_potok' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function direction(): BelongsTo
    {
        return $this->belongsTo(Direction::class);
    }

    public function workloads(): HasMany
    {
        return $this->hasMany(Workload::class);
    }

    // ========================================
    // ACCESSOR'LAR
    // ========================================

    /**
     * 1-semestr jami soatlari
     */
    public function getSemester1TotalHoursAttribute(): float
    {
        return $this->semester_1_lecture + 
               $this->semester_1_practical + 
               $this->semester_1_laboratory + 
               $this->semester_1_seminar + 
               $this->semester_1_practice + 
               $this->semester_1_exam + 
               $this->semester_1_test;
    }

    /**
     * 2-semestr jami soatlari
     */
    public function getSemester2TotalHoursAttribute(): float
    {
        return $this->semester_2_lecture + 
               $this->semester_2_practical + 
               $this->semester_2_laboratory + 
               $this->semester_2_seminar + 
               $this->semester_2_practice + 
               $this->semester_2_exam + 
               $this->semester_2_test;
    }

    /**
     * 1-semestr auditoriya soatlari
     */
    public function getSemester1AuditoryHoursAttribute(): float
    {
        return $this->semester_1_lecture + 
               $this->semester_1_practical + 
               $this->semester_1_laboratory + 
               $this->semester_1_seminar;
    }

    /**
     * 2-semestr auditoriya soatlari
     */
    public function getSemester2AuditoryHoursAttribute(): float
    {
        return $this->semester_2_lecture + 
               $this->semester_2_practical + 
               $this->semester_2_laboratory + 
               $this->semester_2_seminar;
    }

    /**
     * Jami auditoriya soatlari
     */
    public function getTotalAuditoryHoursAttribute(): float
    {
        return $this->semester_1_auditory_hours + $this->semester_2_auditory_hours;
    }

    // ========================================
    // SCOPE'LAR
    // ========================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeByDirection($query, $directionId)
    {
        return $query->where('direction_id', $directionId);
    }

    public function scopeByCourseLevel($query, $level)
    {
        return $query->where('course_level', $level);
    }

    public function scopeBySubjectType($query, $type)
    {
        return $query->where('subject_type', $type);
    }

    public function scopeByEducationForm($query, $form)
    {
        return $query->where('education_form', $form);
    }

    public function scopeCanBePotok($query)
    {
        return $query->where('can_be_potok', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%");
        });
    }

    // ========================================
    // HELPER METODLAR
    // ========================================

    /**
     * Berilgan semestr uchun soatlarni olish
     */
    public function getSemesterHours(int $semester): array
    {
        $prefix = "semester_{$semester}_";
        
        return [
            'lecture' => $this->{$prefix . 'lecture'},
            'practical' => $this->{$prefix . 'practical'},
            'laboratory' => $this->{$prefix . 'laboratory'},
            'seminar' => $this->{$prefix . 'seminar'},
            'practice' => $this->{$prefix . 'practice'},
            'exam' => $this->{$prefix . 'exam'},
            'test' => $this->{$prefix . 'test'},
        ];
    }

    /**
     * Patok qilish mumkinmi tekshirish
     */
    public function canCreatePotok(int $groupsCount): bool
    {
        return $this->can_be_potok && $groupsCount >= $this->min_groups_for_potok;
    }

    /**
     * Taqsimlangan soatlarni hisoblash
     */
    public function getDistributedHours(int $academicYearId, int $semester): array
    {
        $distributed = $this->workloads()
            ->where('academic_year_id', $academicYearId)
            ->where('semester', $semester)
            ->selectRaw('
                SUM(lecture_hours) as lecture,
                SUM(practical_hours) as practical,
                SUM(laboratory_hours) as laboratory,
                SUM(seminar_hours) as seminar,
                SUM(practice_hours) as practice,
                SUM(exam_hours) as exam,
                SUM(test_hours) as test
            ')
            ->first();

        return [
            'lecture' => (float) ($distributed->lecture ?? 0),
            'practical' => (float) ($distributed->practical ?? 0),
            'laboratory' => (float) ($distributed->laboratory ?? 0),
            'seminar' => (float) ($distributed->seminar ?? 0),
            'practice' => (float) ($distributed->practice ?? 0),
            'exam' => (float) ($distributed->exam ?? 0),
            'test' => (float) ($distributed->test ?? 0),
        ];
    }

    /**
     * Qolgan soatlarni hisoblash
     */
    public function getRemainingHours(int $academicYearId, int $semester): array
    {
        $semesterHours = $this->getSemesterHours($semester);
        $distributed = $this->getDistributedHours($academicYearId, $semester);

        return [
            'lecture' => max(0, $semesterHours['lecture'] - $distributed['lecture']),
            'practical' => max(0, $semesterHours['practical'] - $distributed['practical']),
            'laboratory' => max(0, $semesterHours['laboratory'] - $distributed['laboratory']),
            'seminar' => max(0, $semesterHours['seminar'] - $distributed['seminar']),
            'practice' => max(0, $semesterHours['practice'] - $distributed['practice']),
            'exam' => max(0, $semesterHours['exam'] - $distributed['exam']),
            'test' => max(0, $semesterHours['test'] - $distributed['test']),
        ];
    }

    /**
     * Soatlar to'liq taqsimlangan mi?
     */
    public function isFullyDistributed(int $academicYearId, int $semester): bool
    {
        $remaining = $this->getRemainingHours($academicYearId, $semester);
        
        return array_sum($remaining) == 0;
    }

    /**
     * Taqsimlash foizi
     */
    public function getDistributionPercentage(int $academicYearId, int $semester): float
    {
        $semesterHours = $this->getSemesterHours($semester);
        $total = array_sum($semesterHours);
        
        if ($total == 0) {
            return 0;
        }

        $distributed = $this->getDistributedHours($academicYearId, $semester);
        $distributedTotal = array_sum($distributed);

        return round(($distributedTotal / $total) * 100, 2);
    }


    
}