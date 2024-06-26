<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;


/**
 *
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $course_school_day_id Учебный день
 * @property int $group_id Группа
 * @property string $date Дата
 * @property int $order
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CourseSchoolDay> $courseSchoolDay
 * @property-read int|null $course_school_day_count
 * @property-read \App\Models\Group $group
 * @method static Builder|GroupSchoolDay newModelQuery()
 * @method static Builder|GroupSchoolDay newQuery()
 * @method static Builder|GroupSchoolDay query()
 * @method static Builder|GroupSchoolDay whereCourseSchoolDayId($value)
 * @method static Builder|GroupSchoolDay whereCreatedAt($value)
 * @method static Builder|GroupSchoolDay whereDate($value)
 * @method static Builder|GroupSchoolDay whereGroupId($value)
 * @method static Builder|GroupSchoolDay whereId($value)
 * @method static Builder|GroupSchoolDay whereUpdatedAt($value)
 * @method static Builder|GroupSchoolDay whereOrder($value)
 * @mixin \Eloquent
 */
class GroupSchoolDay extends Model
{
    use HasFactory;

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function courseSchoolDay()
    {
        return $this->hasMany(CourseSchoolDay::class);
    }
}
