<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\RestController;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;


class UserController extends RestController
{

    public function index(Group $group)
    {
        $users = User::orderBy('id')->get()
            ->map(function (User $user) {
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
                ];
            });

        $teachers = $users->filter(function ($user) use ($group) {
            $groups = $user['groups']->toArray();
            return isset($groups[$group->id]) && $groups[$group->id] == 'teacher';
        });

        $disciples = $users->filter(function ($user) use ($group) {
            $groups = $user['groups']->toArray();
            return isset($groups[$group->id]) && $groups[$group->id] == 'disciple';
        });

        return $this->ResponseOk(['teachers' => $teachers, 'disciples' => $disciples]);
    }


    /**
     * Зачислить ученика в группу.
     */
    public function joinUserToGroup(Group $group, User $user, $role)
    {
        $PermissionsGroup = $user->PermissionsGroup($group->id);
        if (!empty($PermissionsGroup)) {
            return $this->ResponseError("Пользователь уже подключен к группе с ролью '$PermissionsGroup'");
        }

        $GroupUser = new GroupUser();
        $GroupUser->group_id = $group->id;
        $GroupUser->user_id = $user->id;
        $GroupUser->role = $role;
        $GroupUser->save();

        return $this->index($group);
    }

    /**
     * Удалить юзера из группы.
     */
    public function delUserFromGroup(Group $group, User $user)
    {
        $GroupUser = GroupUser::whereGroupId($group->id)->whereUserId($user->id)->first();
        $GroupUser->delete();

        return $this->index($group);
    }

}
