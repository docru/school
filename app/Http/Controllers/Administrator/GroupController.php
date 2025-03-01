<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupSchoolDay;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return $this->ResponseOk($this->_groupList());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
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
    public function show(Group $group): JsonResponse
    {
        $groupsSchoolDay = $group->groupsSchoolDay->mapWithKeys(function (GroupSchoolDay $day) {
            return [$day->course_school_day_id => [
                'id' => $day->id,
                'date' => date('d.m', strtotime($day->date)),
                'dateOrigin' => $day->date,
                'status' => $day->status,
            ]];
        });
        $group = $group->toArray();
        $group['groups_school_day'] = $groupsSchoolDay;
        return $this->ResponseOk($group);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group): JsonResponse
    {
        $group->update($request->all());
        return $this->ResponseOk($group->toArray());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group): JsonResponse
    {
        $group->delete();
        return $this->ResponseOk($this->_groupList());
    }

    private function _groupList(): array
    {
        return Group::all()->toArray();
    }


    /**
     * Список пользователей группы
     * @param Group $group
     * @return JsonResponse
     */
    public function users(Group $group): JsonResponse
    {
        $users = $group->users;
        return $this->ResponseOk($users->toArray());
    }


    public function addGroupsSchoolDay(Request $request, Group $group): JsonResponse
    {
        $date = $request->post('date');
        if (empty($date)) {
            return $this->ResponseError("Не задана дата нового учебного дня");
        }
        $date = date('Y-m-d', strtotime($date));

        // Закрыть открытые учебные дни
        $group->groupsSchoolDay()->whereStatus('open')->update(['status' => 'close']);

        $lastGroupsSchoolDay = $group->groupsSchoolDay()->orderByDesc('date')->first();
        $lastOrder = empty($lastGroupsSchoolDay) ? 0 : $lastGroupsSchoolDay->order;

        $nextCourseSchoolDays = $group->course
            ->course_school_days()
            ->where('order', '>', $lastOrder)
            ->orderBy('order')
            ->first();

        if (empty($nextCourseSchoolDays)) {
            return $this->ResponseError('В курсе больше нет учебных дней');
        } else {
            $newGroupSchoolDays = new GroupSchoolDay();
            $newGroupSchoolDays->group_id = $group->id;
            $newGroupSchoolDays->course_school_day_id = $nextCourseSchoolDays->id;
            $newGroupSchoolDays->date = $date;
            $newGroupSchoolDays->order = $nextCourseSchoolDays->order;
            $newGroupSchoolDays->save();
        }

        return $this->show($group);
    }


    public function closeGroupsSchoolDay(Request $request, Group $group): JsonResponse
    {
        $group->groupsSchoolDay()->whereStatus('open')->update(['status' => 'close']);
        return $this->show($group);
    }

    public function changeGroupsSchoolDay(Request $request, Group $group, GroupSchoolDay $groupSchoolDay): JsonResponse
    {
        $date = date('Y-m-d', strtotime($request->post('date')));

        $find = GroupSchoolDay::whereGroupId($group->id)->whereDate('date', $date)->first();
        if (!empty($find) && $find->id !== $groupSchoolDay->id) {
            return $this->ResponseError('Эта дата уже задана для группы');
        }

        $groupSchoolDay->date = $date;
        $groupSchoolDay->save();

        return $this->show($group);
    }

    public function setStatusGroup(Request $request, Group $group, string $status): JsonResponse
    {
        if (!in_array($status, ['open', 'archived'])) {
            return $this->ResponseError('Не верный новый статус группы');
        }

        $group->status = $status;
        $group->save();

        return $this->show($group);
    }

}
