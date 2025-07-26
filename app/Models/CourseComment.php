<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseComment extends Model
{
    protected $fillable = ['course_id', 'user_id', 'parent_id', 'comment'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(CourseComment::class, 'parent_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(CourseComment::class, 'parent_id');
    }
}
