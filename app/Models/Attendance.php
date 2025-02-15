<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 *
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $group_school_day_id Учебный день группы
 * @property int $user_id Ученик
 * @method static Builder|Attendance newModelQuery()
 * @method static Builder|Attendance newQuery()
 * @method static Builder|Attendance query()
 * @method static Builder|Attendance whereCreatedAt($value)
 * @method static Builder|Attendance whereGroupSchoolDayId($value)
 * @method static Builder|Attendance whereId($value)
 * @method static Builder|Attendance whereUpdatedAt($value)
 * @method static Builder|Attendance whereUserId($value)
 * @property-read GroupSchoolDay $groupSchoolDay
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 * @mixin \Eloquent
 */
class Attendance extends Model
{
    use HasFactory;

    public function groupSchoolDay()
    {
        return $this->belongsTo(GroupSchoolDay::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
