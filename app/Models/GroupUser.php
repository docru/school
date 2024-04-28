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
 * @property string|null $deleted_at
 * @property int $user_id Пользователь
 * @property int $group_id Группа
 * @property string $role Роль в группе
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GroupSchoolDay> $groupSchoolDays
 * @property-read int|null $group_school_days_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $students
 * @property-read int|null $students_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $teachers
 * @property-read int|null $teachers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|GroupUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupUser whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupUser whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupUser whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupUser whereUserId($value)
 * @mixin \Eloquent
 */
class GroupUser extends Model
{
    use HasFactory;

    public function users()
    {
        return $this->belongsToMany(User::class);
    }


    public function teachers()
    {
        return $this->belongsToMany(User::class)->whereRole('teacher');
    }


    public function students()
    {
        return $this->belongsToMany(User::class)->whereRole('student');
    }

    public function groupSchoolDays()
    {
        return $this->hasMany(GroupSchoolDay::class);
    }
}
