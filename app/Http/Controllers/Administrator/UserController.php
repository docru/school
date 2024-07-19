<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\RestController;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;


class UserController extends RestController
{

    public function index(Group $group, $msgOk = false)
    {
        $users = User::orderBy('id')->get()
            ->map(function (User $user) use ($group) {
                $groupUser = $user->groupUsers()->withTrashed()->whereGroupId($group->id)->first(['role', 'status', 'deleted_at']);
                return [
                    'id' => $user->id,
                    'phone' => $user->phone,
                    'authorized_at' => $user->authorized_at,
                    'name' => $user->name,
                    'nickname' => $user->nickname,
                    'entry_code' => $user->entry_code ? env('APP_URL') . '/login/' . $user->entry_code : '',
                    'role' => (!empty($groupUser)) ? $groupUser->role : '',
                    'status' => (!empty($groupUser)) ? $groupUser->status : '',
                    'deleted_at' => (!empty($groupUser)) ? $groupUser->deleted_at : '',
                ];
            });

        $teachers = $users->filter(function ($user) {
            return $user['role'] == 'teacher';
        });

        $disciples = $users->filter(function ($user) {
            return $user['role'] == 'disciple';
        });

        $res = ['teachers' => $teachers, 'disciples' => $disciples];
        if (!!$msgOk) {
            $res['msgOk'] = $msgOk;
        }

        return $this->ResponseOk($res);
    }


    /**
     * Зачислить ученика в группу.
     */
    public function joinUserToGroup(Group $group, User $user, $role)
    {
        if (!in_array($role, ['teacher', 'disciple'])) {
            return $this->ResponseError("Нельзя присоединить к группе с ролью '$role'");
        }
        $PermissionsGroup = $user->PermissionsGroup($group->id);
        if (!empty($PermissionsGroup)) {
            return $this->ResponseError("Пользователь уже подключен к группе с ролью '$PermissionsGroup'");
        }

        $GroupUser = new GroupUser();
        $GroupUser->group_id = $group->id;
        $GroupUser->user_id = $user->id;
        $GroupUser->role = $role;
        $GroupUser->save();

        if (!$user->hasRole($role)) {
            $user->addRole($role);
        }

        return $this->index($group);
    }

    /**
     * Удалить юзера из группы.
     */
    public function removeUserFromGroup(Group $group, User $user)
    {
        $GroupUser = GroupUser::whereGroupId($group->id)->whereUserId($user->id)->first();
        if (!empty($GroupUser)) {
            $roleName = $GroupUser->roleName();
            $roleName = mb_strtoupper(mb_substr($roleName, 0, 1)) . mb_substr($roleName, 1);


            $action = 'удален';
            if ($GroupUser->role == 'disciple' && $GroupUser->attendances()->count() > 0) {
                $GroupUser->status = 'expelled';
                $GroupUser->save();
                $GroupUser->delete();
                $action = 'отчислен';
            } else {
                $GroupUser->forceDelete();
            }

            $msgOk = "$roleName $action из группы";

            return $this->index($group, $msgOk);
        } else {
            return $this->ResponseError("Такого пользователя в группе нет.");
        }

    }

}
