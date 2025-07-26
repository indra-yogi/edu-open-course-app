<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'thumbnail',
        'is_approved'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(CourseMaterial::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(CourseLike::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(CourseComment::class)->whereNull('parent_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(CourseCategory::class, 'course_category_pivot');
    }

    public function getLikesCountAttribute(): int
    {
        return $this->likes()->count();
    }
}
