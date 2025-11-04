<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Faculty extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'dean_id',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get dean
     */
    public function dean(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dean_id');
    }

    /**
     * Get departments
     */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    /**
     * Get directions
     */
    public function directions(): HasMany
    {
        return $this->hasMany(Direction::class);
    }

    /**
     * Get groups through directions
     * Faculty → Direction → Group
     */
    public function groups(): HasManyThrough
    {
        return $this->hasManyThrough(
            Group::class,      // Final model
            Direction::class,  // Intermediate model
            'faculty_id',      // Foreign key on directions table
            'direction_id',    // Foreign key on groups table
            'id',              // Local key on faculties table
            'id'               // Local key on directions table
        );
    }

    /**
     * Scope for active faculties
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}