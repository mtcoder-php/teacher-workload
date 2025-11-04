<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Direction extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'department_id',
        'degree_type',
        'duration_years',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'duration_years' => 'integer',
    ];

    /**
     * Get department
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get faculty through department
     */
    public function getFacultyAttribute()
    {
        return $this->department?->faculty;
    }

    /**
     * Get groups
     */
    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    /**
     * Scope for active directions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for faculty (through department)
     */
    public function scopeByFaculty($query, $facultyId)
    {
        return $query->whereHas('department', function($q) use ($facultyId) {
            $q->where('faculty_id', $facultyId);
        });
    }

    /**
     * Scope for bakalavr
     */
    public function scopeBakalavr($query)
    {
        return $query->where('degree_type', 'bakalavr');
    }

    /**
     * Scope for magistratura
     */
    public function scopeMagistratura($query)
    {
        return $query->where('degree_type', 'magistratura');
    }

    /**
     * Get max course
     */
    public function getMaxCourseAttribute()
    {
        return $this->duration_years;
    }
}
