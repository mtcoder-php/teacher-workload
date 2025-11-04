<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subject> $activeSubjects
 * @property-read int|null $active_subjects_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Teacher> $activeTeachers
 * @property-read int|null $active_teachers_count
 * @property-read \App\Models\Faculty|null $faculty
 * @property-read \App\Models\User|null $head
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subject> $subjects
 * @property-read int|null $subjects_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Teacher> $teachers
 * @property-read int|null $teachers_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department query()
 * @mixin \Eloquent
 */
class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'faculty_id',
        'name',
        'code',
        'head_id',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Kafedraning fakulteti
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Kafedraning mudiri
     */
    public function head(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_id');
    }

    /**
     * Kafedradagi o'qituvchilar
     */
    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }

    /**
     * Kafedradagi fanlar
     */
    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    /**
     * Kafedradagi yo'nalishlar
     */
    public function directions(): HasMany
    {
        return $this->hasMany(Direction::class);
    }

    /**
     * Aktiv o'qituvchilarni olish
     */
    public function activeTeachers(): HasMany
    {
        return $this->teachers()->where('is_active', true);
    }

    /**
     * Aktiv fanlarni olish
     */
    public function activeSubjects(): HasMany
    {
        return $this->subjects()->where('is_active', true);
    }

    /**
     * Faqat faol kafedralarni olish
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}