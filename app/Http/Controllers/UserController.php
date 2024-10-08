<?php

namespace App\Http\Controllers;

use App\Models\GroupUser;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class UserController extends RestController
{

    /**
     * Авторизация
     * @param $entryCode
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login($entryCode)
    {
        // TODO - чистить устаревшие коды входа
        $user = User::whereEntryCode($entryCode)->first();
        if (!empty($user)) {
            Auth::login($user);
            // TODO - чистить код входа
//            $user->entry_code = null;
            $user->authorized_at = date('Y-m-d H:i:s');
            $user->save();
        }
        return redirect()->to('/');
    }

    public function home()
    {
        $user = Auth::user();
        $arRes = [];
        if ($user->hasRole('teacher')) {
            $groups = GroupUser::whereRole('teacher')
                ->whereUserId(auth()->id())
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function (GroupUser $groupUser) {
                    $group = $groupUser->group;
                    $course = $group->course;

                    $res = [
                        'id' => $groupUser->id,
                        'status' => $groupUser->status,
                        'group' => [
                            'id' => $group->id,
                            'name' => $group->name,
                        ],
                        'course' => [
                            'id' => $course->id,
                            'name' => $course->name,
                        ],
                    ];
                    return $res;
                })->toArray();
            $arRes['teacher'] = $groups;
        }

        if ($user->hasRole('disciple')) {
            $groups = GroupUser::whereRole('disciple')
                ->whereUserId(auth()->id())
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function (GroupUser $groupUser) {
                    $group = $groupUser->group;
                    $course = $group->course;

                    $res = [
                        'id' => $groupUser->id,
                        'status' => $groupUser->status,
                        'group' => [
                            'id' => $group->id,
                            'name' => $group->name,
                        ],
                        'course' => [
                            'id' => $course->id,
                            'name' => $course->name,
                        ],
                    ];
                    return $res;
                })->toArray();
            $arRes['disciple'] = $groups;
        }

        return $this->ResponseOk($arRes);
    }

    /**
     * Создать пользователя
     * @return \Illuminate\Http\JsonResponse
     */
    public function create()
    {

        $phone = User::phoneNormalize(request()->post('phone'));
        if (!$phone) {
            return $this->ResponseError('Не верный формат телефона');
        }
        $user = User::wherePhone($phone)->first();
        if (!empty($user)) {
            return $this->ResponseError('Пользователь с таким телефоном уже зарегистрирован');
        }

        $roles = request()->post('roles');
        $role = request()->post('role');
        if (empty($roles) && !empty($role)) {
            $roles = [$role];
        }
        if (empty($roles)) {
            return $this->ResponseError('Не заданы роли');
        }
        $surname = request()->post('surname');
        if (empty($surname)) {
            return $this->ResponseError('Не задана фамилия');
        }
        $name = request()->post('surname');
        if (empty($name)) {
            return $this->ResponseError('Не задано имя');
        }
        $patronymic = request()->post('patronymic');

        if (!auth()->user()->hasRole('superadmin')) {
            if (auth()->user()->hasRole('administrator') && ($roles == ['disciple'] || $roles == ['teacher'])) {

            } else {
                return $this->ResponseError('У вас не достаточно прав для создания пользователя');
            }
        }

        $user = User::create([
            'surname' => $surname,
            'name' => $name,
            'patronymic' => $patronymic,
            'phone' => $phone,
        ]);

        $existsRoles = Role::all()->map(function (Role $role) {
            return $role->name;
        })->toArray();

        $roles = array_intersect($roles, $existsRoles);
        $user->addRoles($roles);
        return $this->list($role);
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
    public function list($role = false)
    {
        if (
            !empty(auth()->user())
            && (auth()->user()->hasRole('superadmin')
                || (auth()->user()->hasRole('administrator') && in_array($role, ['disciple', 'teacher'])))
        ) {

        } else {
            return $this->ResponseError('Нет прав на получение этого списка');
        }

        if (!empty($role)) {
            $users = User::whereHasRole($role)->orderBy('id')->get();
        } else {
            $users = User::orderBy('id')->get();
        }

        $users = $users->map(function (User $user) {
            return [
                'id' => $user->id,
                'phone' => $user->phone,
                'authorized_at' => $user->authorized_at,
                'surname' => $user->surname,
                'name' => $user->name,
                'patronymic' => $user->patronymic,
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
        $roles = Role::all()->map(function (Role $role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'display_name' => $role->display_name,
                'description' => $role->description,
            ];
        })->toArray();
        return $this->ResponseOk($roles);
    }

    /**
     * Список пользователей группы
     * @param $groupId
     * @return void
     */
    public function disciples($groupId)
    {
        //
    }

    /**
     * Профиль пользователя
     */
    public function profile($uid = false)
    {
        if ($uid) {
            $user = User::whereId($uid)->first();
        } else {
            $user = auth()->user();
        }
        $roles = $user->roles->map(function ($item) {
            return $item->name;
        })->toArray();

        return $this->ResponseOk([
            'id' => $user->id,
            'phone' => $user->phone,
            'surname' => $user->surname,
            'name' => $user->name,
            'patronymic' => $user->patronymic,
            'roles' => $roles,
        ]);
    }

    /**
     * Сохранение профиля
     */
    public function profileSave(Request $request)
    {
        $user = auth()->user();
        $user->name = $request->name;
        $user->surname = $request->surname;
        $user->patronymic = $request->patronymic;
        $user->save();

        return $this->profile();
    }

}
