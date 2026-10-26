<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * 
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name Имя группы
 * @property int $course_id Курс
 * @property string $status Статус группы
 * @property-read \App\Models\Course $course
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GroupUser> $groupUser
 * @property-read int|null $group_user_count
 * @method static \Illuminate\Database\Eloquent\Builder|Group newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Group newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Group query()
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GroupSchoolDay> $groupsSchoolDay
 * @property-read int|null $groups_school_day_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @mixin \Eloquent
 */
class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'course_id'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function groupUser(): HasMany
    {
        return $this->hasMany(GroupUser::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_users');
    }

    public function groupsSchoolDay(): HasMany
    {
        return $this->hasMany(GroupSchoolDay::class);
    }

    /**
     * Сводка по группе для списков: статус, прогресс по занятиям, состав.
     * Лежит в модели, потому что одинаково нужна и администратору, и учителю.
     */
    public function summary(): array
    {
        $teachers = $this->groupUser
            ->where('role', 'teacher')
            ->where('status', 'active')
            ->map(fn(GroupUser $groupUser): string => trim(
                ($groupUser->user?->surname ?? '') . ' ' . ($groupUser->user?->name ?? '')
            ))
            ->filter()
            ->values();

        $disciples = $this->groupUser->where('role', 'disciple');

        $days = $this->groupsSchoolDay->sortBy('order')->values();
        $openDay = $days->firstWhere('status', 'open');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'course_id' => $this->course_id,
            'course_name' => $this->course?->name ?? '',
            'created_at' => $this->created_at?->format('Y-m-d'),
            'teachers' => $teachers->all(),
            'disciples_active' => $disciples->where('status', 'active')->count(),
            'disciples_expelled' => $disciples->where('status', 'expelled')->count(),
            'days_closed' => $days->where('status', 'close')->count(),
            'days_total' => $this->course?->course_school_days->count() ?? 0,
            'first_day' => $days->first()?->date,
            'last_day' => $days->last()?->date,
            'open_day' => $openDay?->date,
        ];
    }
}
