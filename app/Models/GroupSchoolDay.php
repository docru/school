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
 * @property int $course_school_day_id Учебный день
 * @property int $group_id Группа
 * @property string $date Дата
 * @method static \Illuminate\Database\Eloquent\Builder|GroupSchoolDay newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupSchoolDay newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupSchoolDay query()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupSchoolDay whereCourseSchoolDayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupSchoolDay whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupSchoolDay whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupSchoolDay whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupSchoolDay whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupSchoolDay whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GroupSchoolDay extends Model
{
    use HasFactory;
}
