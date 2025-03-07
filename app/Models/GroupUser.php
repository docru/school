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
 * @property string|null $expelled_at Дата отчисления
 * @property int $user_id Пользователь
 * @property int $group_id Группа
 * @property string $role Роль в группе
 * @property string $status Статус
 * @property-read Group $group
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|GroupUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupUser whereExpelledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupUser whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupUser whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupUser whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupUser whereUserId($value)
 * @mixin \Eloquent
 */
class GroupUser extends Model
{
    use HasFactory;

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function group(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function attendances(): Attendance|\Illuminate\Database\Eloquent\Builder
    {
        return Attendance::whereUserId($this->user_id);
    }

    public function roleName(): string
    {
        return $this->role == 'teacher' ? 'учитель' : ($this->role == 'disciple' ? 'ученик' : '');
    }

}
