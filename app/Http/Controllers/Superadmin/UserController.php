<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RestController;
use App\Models\Lesson;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use mysql_xdevapi\Exception;


class UserController extends RestController
{

    /**
     * Создать пользователя
     * @return void
     */
    public function store()
    {

        $phone = User::phoneNormalize(request()->post('phone'));
        if (!$phone) {
            return $this->ResponseError('Не верный формат телефона');
        }
        $user = User::wherePhone($phone)->first();
        if (!empty($user)) {
            return $this->ResponseError('Пользователь с таким телефоном уже зарегистрирован');
        }
        $user = User::create(['phone' => $phone]);

        $roles = request()->post('roles');


        $existsRoles = Role::all()->map(function (Role $role) {
            return $role->name;
        })->toArray();

        $roles = array_intersect($roles, $existsRoles);
        $user->addRoles($roles);
//        return $this->list();
        return $this->ResponseOk($this->list());


    }

    /**
     * Сгенерировать ссылку для авторизации
     * @return string
     */
    public function authLink($uid)
    {
        // Проверитиь права на генерацию ссылки
        if (empty(auth()->user()) || !auth()->user()->hasRole(['superadmin', 'administrator'])) {
            return '';
        }
        $user = User::whereId($uid)->first();
        if (empty($user)) {
            return 'Не такого пользователя';
        }

        $user->entry_code = Str::random(12);
        $user->entry_code_generated_at = date('Y-m-d H:i:s');
        $user->save();

        return $this->ResponseOk([
            'key' => env('APP_URL') . '/login/' . $user->entry_code,
            'id' => $user->id
        ]);
    }

    /**
     * Список пользователей.
     */
    public function list()
    {
        $users = User::all()->sortBy('id')->map(function (User $user) {
            return [
                'id' => $user->id,
                'phone' => $user->phone,
                'authorized_at' => $user->authorized_at,
                'name' => $user->name,
                'nickname' => $user->nickname,
                'entry_code' => $user->entry_code ? env('APP_URL') . '/login/' . $user->entry_code : '',
                'roles' => $user->roles->map(function (Role $role) {
                    return ['name' => $role->name, 'display_name' => $role->display_name];
                }),
            ];
        });

        return $this->ResponseOk(['users' => $users]);
    }

    public function roles()
    {
        return $this->ResponseOk(['roles' => Role::all()->map(function (Role $role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'display_name' => $role->display_name,
                'description' => $role->description,
            ];
        })]);
    }

}
