<?php

declare(strict_types=1);

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupSchoolDay;
use App\Models\GroupUser;
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

    /**
     * Список групп со сводкой для карточек: курс, учителя, ученики, прогресс по учебным дням.
     * @return array<int, array<string, mixed>>
     */
    private function _groupList(): array
    {
        $groups = Group::query()
            ->with([
                'course:id,name',
                'course.course_school_days:id,course_id',
                'groupUser.user:id,name,surname,patronymic',
                'groupsSchoolDay',
            ])
            ->orderByDesc('id')
            ->get();

        return $groups
            ->map(fn(Group $group): array => $this->_groupSummary($group))
            ->values()
            ->all();
    }

    /**
     * Сводка по одной группе для списка
     * @param Group $group
     * @return array<string, mixed>
     */
    private function _groupSummary(Group $group): array
    {
        $teachers = $group->groupUser
            ->where('role', 'teacher')
            ->where('status', 'active')
            ->map(fn(GroupUser $groupUser): string => trim(
                ($groupUser->user?->surname ?? '') . ' ' . ($groupUser->user?->name ?? '')
            ))
            ->filter()
            ->values();

        $disciples = $group->groupUser->where('role', 'disciple');

        $days = $group->groupsSchoolDay->sortBy('order')->values();
        $openDay = $days->firstWhere('status', 'open');

        return [
            'id' => $group->id,
            'name' => $group->name,
            'status' => $group->status,
            'course_id' => $group->course_id,
            'course_name' => $group->course?->name ?? '',
            'created_at' => $group->created_at?->format('Y-m-d'),
            'teachers' => $teachers->all(),
            'disciples_active' => $disciples->where('status', 'active')->count(),
            'disciples_expelled' => $disciples->where('status', 'expelled')->count(),
            'days_closed' => $days->where('status', 'close')->count(),
            'days_total' => $group->course?->course_school_days->count() ?? 0,
            'first_day' => $days->first()?->date,
            'last_day' => $days->last()?->date,
            'open_day' => $openDay?->date,
        ];
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
