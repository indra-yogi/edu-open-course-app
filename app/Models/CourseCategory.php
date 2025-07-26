<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CourseCategory extends Model
{
    protected $fillable = ['name'];

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_category_pivot','category_id','course_id');
    }
}
