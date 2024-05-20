<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\RestController;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;


class UserController extends RestController
{

    public function index()
    {
        $users = User::whereHasRole('disciple')->orderBy('id')->get()->map(function (User $user) {
            return [
                'id' => $user->id,
                'phone' => $user->phone,
                'authorized_at' => $user->authorized_at,
                'name' => $user->name,
                'nickname' => $user->nickname,
                'entry_code' => $user->entry_code ? env('APP_URL') . '/login/' . $user->entry_code : '',
                'groups' => $user->groupUsers->mapWithKeys(function (GroupUser $groupUser) {
                    return [$groupUser->group_id => $groupUser->role];
                }),
//                'roles' => $user->roles->map(function (Role $role) {
//                    return ['name' => $role->name, 'display_name' => $role->display_name];
//                }),
            ];
        })->toArray();
        return $this->ResponseOk(['users' => $users]);
    }

    /**
     * Создать пользователя
     * @return \Illuminate\Http\JsonResponse
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

        $user->addRoles(['disciple']);
        return $this->index();
    }

    /**
     * Сгенерировать ссылку для авторизации
     * @param $uid
     * @return \Illuminate\Http\JsonResponse|string
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

}
