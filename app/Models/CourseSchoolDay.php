<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $course_id Курс
 * @property int $order Сортировка
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSchoolDay newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSchoolDay newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSchoolDay query()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSchoolDay whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSchoolDay whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSchoolDay whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSchoolDay whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSchoolDay whereUpdatedAt($value)
 * @property-read \App\Models\Course $course
 * @mixin \Eloquent
 */
class CourseSchoolDay extends Model
{
    use HasFactory;

    protected static function booted () {
        static::deleting(function(CourseSchoolDay $courseSchoolDay) {
            $courseSchoolDay->lessons()->update(['school_day' => null, 'school_day_order' => null]);
        });
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function lessons()
    {
        return Lesson::whereSchoolDay($this->id);
    }
}
