<?php

namespace App\Http\Controllers\Disciple;

use App\Http\Controllers\Controller;
use App\Models\CourseSchoolDay;
use App\Models\Group;
use App\Models\GroupSchoolDay;
use App\Models\GroupUser;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groups = GroupUser::whereRole('disciple')
            ->whereUserId(auth()->id())
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function (GroupUser $groupUser) {
                $group = $groupUser->group;
                $course = $group->course;

                $res =[
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
        return $this->ResponseOk($groups);
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        $course = $group->course;
        $groupsSchoolDay = $group->groupsSchoolDay->mapWithKeys(function (GroupSchoolDay $item) {
            return [$item->course_school_day_id => [
                'id' => $item->id,
                'date' => $item->date,
                'order' => $item->order,
                'status' => $item->status,
            ]];
        });

        $courseSchoolDay = $course->course_school_days()->get(['id', 'order'])->map(function (CourseSchoolDay $item) {
            $lessons = $item->lessons()->orderBy('school_day_order')->get(['id', 'name', 'school_day_order', 'hours']);
            return [
                'id' => $item->id,
                'order' => $item->order,
                'lessons' => $lessons,
            ];
        });

        // группа (название)
        // учителя (список)
        // ученики (список)
        // курс (название)
        // расписание (все дни расписания, уроки в днях, дни группы со статусом)
        // посещаемость (присутствовал/отсутствовал/?)
        // статус в группе (в процессе обучения / отчислен / переведен)

        return $this->ResponseOk([
            'group' => [
                'id' => $group->id,
                'name' => $group->name,
            ],
            'course' => [
                'id' => $course->id,
                'name' => $course->name,
            ],
            'courseSchoolDay' => $courseSchoolDay,
            'groupsSchoolDay' => $groupsSchoolDay,
        ]);
    }
}
