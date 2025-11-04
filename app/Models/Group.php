<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// ✅ YANGI: HasMany o'rniga BelongsToMany'ni import qilamiz
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'direction_id',
        'course',
        'education_type',
        'education_language',
        'student_count',
        'is_active',
    ];

    protected $casts = [
        'course' => 'integer',
        'student_count' => 'integer',
        'is_active' => 'boolean',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function direction(): BelongsTo
    {
        return $this->belongsTo(Direction::class);
    }

    /**
     * ✅ O'ZGARTIRILDI: Guruh ishtirok etayotgan barcha yuklamalar.
     * Endi bu 'workload_groups' jadvali orqali ishlaydi.
     */
    public function workloads(): BelongsToMany
    {
        return $this->belongsToMany(Workload::class, 'workload_groups');
    }

    // ==========================================
    // BOOT & EVENTS
    // ==========================================

    /**
     * ❌ O'CHIRILDI: booted() metodi.
     * Sabab: Bu mantiq endi ishlamaydi va xatolikka olib keladi,
     * chunki `workloads` jadvalida `group_id` yo'q.
     * Talabalar soni o'zgarganda yuklamani yangilashni Service qatlamida
     * yoki alohida Job (navbat) orqali qilish to'g'riroq bo'ladi.
     */
    // protected static function booted(): void { ... }

    // ==========================================
    // ACCESSORS
    // ==========================================

    public function getEducationTypeNameAttribute(): string
    {
        return match ($this->education_type) {
            'kunduzgi' => 'Kunduzgi',
            'sirtqi' => 'Sirtqi',
            'kechki' => 'Kechki',
            'masofaviy' => 'Masofaviy',
            default => $this->education_type,
        };
    }

    public function getEducationLanguageNameAttribute(): string
    {
        return match ($this->education_language) {
            'uzbek' => 'O\'zbek',
            'russian' => 'Rus',
            default => $this->education_language,
        };
    }

    public function getCourseNameAttribute(): string
    {
        return match ($this->course) {
            1 => '1-kurs (Bakalavr)',
            2 => '2-kurs (Bakalavr)',
            3 => '3-kurs (Bakalavr)',
            4 => '4-kurs (Bakalavr)',
            5 => '1-kurs (Magistratura)',
            6 => '2-kurs (Magistratura)',
            default => "{$this->course}-kurs",
        };
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCourse($query, int $course)
    {
        return $query->where('course', $course);
    }

    public function scopeByDirection($query, int $directionId)
    {
        return $query->where('direction_id', $directionId);
    }

    public function scopeByEducationType($query, string $educationType)
    {
        return $query->where('education_type', $educationType);
    }

    // ✅ YANGI SCOPE
    public function scopeByEducationLanguage($query, string $language)
    {
        return $query->where('education_language', $language);
    }
}
