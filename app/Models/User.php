<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laratrust\Contracts\LaratrustUser;
use Laratrust\Traits\HasRolesAndPermissions;

/**
 *
 *
 * @property int $id
 * @property string $phone Телефон
 * @property string|null $surname Фамилия
 * @property string|null $name Имя
 * @property string|null $patronymic Отчество
 * @property string|null $entry_code Код входа
 * @property string|null $entry_code_generated_at Время генерации кода
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $authorized_at Время последней авторизации
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GroupUser> $groupUsers
 * @property-read int|null $group_users_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User orWhereHasPermission(\BackedEnum|array|string $permission = '', ?mixed $team = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User orWhereHasRole(\BackedEnum|array|string $role = '', ?mixed $team = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAuthorizedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDoesntHavePermissions()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDoesntHaveRoles()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEntryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEntryCodeGeneratedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereHasPermission(\BackedEnum|array|string $permission = '', ?mixed $team = null, string $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|User whereHasRole(\BackedEnum|array|string $role = '', ?mixed $team = null, string $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePatronymic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSurname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable implements LaratrustUser
{
    use HasFactory, Notifiable, HasRolesAndPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'surname',
        'name',
        'patronymic',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
//        'entry_code',
    ];


    public function groupUsers()
    {
        return $this->hasMany(GroupUser::class);
    }


    /**
     * @param string $phone
     * @return array|false|string|string[]|null
     */
    public static function phoneNormalize(string $phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) == 10) {
            $phone = '7' . $phone;
        }

        if (substr($phone, 0, 1) == '8') {
            $phone = '7' . substr($phone, 1);
        }

        if (strlen($phone) != 11 || substr($phone, 0, 1) != '7') {
            return false;
        }

        return $phone;
    }

    public function PermissionsGroup($groupId)
    {

        if ($this->hasRole('superadmin')) {
            return 'superadmin';
        }
        $groupUser = $this->groupUsers()->where('group_id', $groupId)->first();
        if (!empty($groupUser)) {
            return $groupUser->role;
        }
        return false;
    }

    /**
     * Обезличить пользователя
     * @return void
     */
    public function depersonalize()
    {
        $roles = $this->roles()->get();
        foreach ($roles as $role) {
            $this->removeRole($role);
        }

        $permissions = $this->permissions()->get();
        foreach ($permissions as $permission) {
            $this->removePermission($permission);
        }

        $this->name = null;
        $this->surname = null;
        $this->patronymic = null;
        $this->entry_code = null;
        $this->entry_code_generated_at = null;
        $this->authorized_at = null;
        $this->deleted_at = date('Y-m-d H:i:s');
        $this->save();
    }

    /**
     * Установить роли
     * @param array $roles
     * @return void
     */
    public function setRoles(array $roles)
    {
        $existsRoles = Role::all()->map(function (Role $role) {
            return $role->name;
        })->toArray();

        $newRoles = collect($roles)
//            ->map(function ($role) {
//                return $role['name'];
//            })
            ->intersect($existsRoles)->toArray();

        $this->syncRoles($newRoles);
    }
}
