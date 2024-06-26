<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\CourseSchoolDay;
use App\Models\Group;
use App\Models\GroupSchoolDay;
use App\Models\GroupUser;
use App\Models\User;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->ResponseOk($this->_groupList());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (empty($request->course_id)) {
            return $this->ResponseError('Не указана группа');
        }
        if (empty($request->name)) {
            return $this->ResponseError('Не указан название группы');
        }

        $group = new Group(['name' => $request->name, 'course_id' => $request->course_id]);
        $group->save();
        if (empty($group->id)) {
            return $this->ResponseError('Ошибка при создании группы');
        }

        return $this->ResponseOk($this->_groupList());
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        $groupsSchoolDay = $group->groupsSchoolDay->mapWithKeys(function (GroupSchoolDay $day){
            return [$day->course_school_day_id => [
                'id' => $day->id,
                'date' => date('d.m', strtotime($day->date)),
            ]];
        });
        $group = $group->toArray();
        $group['groups_school_day'] = $groupsSchoolDay;
        return $this->ResponseOk($group);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group)
    {
        $group->update($request->all());
        return $this->ResponseOk($group->toArray());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        $group->delete();
        return $this->ResponseOk($this->_groupList());
    }

    private function _groupList()
    {
        return Group::all()->toArray();
    }


    /**
     * Список пользователей группы
     * @param $group
     * @return void
     */
    public function users(Group $group)
    {
        $users = $group->users;
        return $this->ResponseOk($users->toArray());
    }


    /**
     * Зачислить ученика в группу.
     */
    public function joinUserToGroup(Request $request)
    {

        $group = Group::whereId($request->post('groupId'))->first();
        $user = User::whereId($request->post('userId'))->first();
        $role = $request->post('role');

        if (empty($group)) {
            return $this->ResponseError("Нет такой группы");
        }
        if (empty($user)) {
            return $this->ResponseError("Нет такого пользователя");
        }
        if (empty($role)) {
            return $this->ResponseError("Не указана роль пользователя");
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

        return $this->users($group);
    }


    public function addGroupsSchoolDay(Request $request, Group $group)
    {
        $date = $request->post('date');
        if (empty($date)) {
            return $this->ResponseError("Не задана дата нового учебного дня");
        }
        $date = date('Y-m-d', strtotime($date));

        $lastGroupsSchoolDay = $group->groupsSchoolDay()->orderByDesc('date')->first();
        $lastOrder = empty($lastGroupsSchoolDay) ? 0 : $lastGroupsSchoolDay->order;

        $nextCourseSchoolDays = $group->course
            ->course_school_days()
            ->where('order', '>' , $lastOrder)
            ->orderBy('order')
            ->first();

        if(!empty($nextCourseSchoolDays)){
            $newGroupSchoolDays = new GroupSchoolDay();
            $newGroupSchoolDays->group_id = $group->id;
            $newGroupSchoolDays->course_school_day_id = $nextCourseSchoolDays->id;
            $newGroupSchoolDays->date = $date;
            $newGroupSchoolDays->order = $nextCourseSchoolDays->order;
            $newGroupSchoolDays->save();

        }

        return $this->show($group);
    }

}
