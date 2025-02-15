<?php

namespace App\Http\Controllers\Disciple;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
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
        if (empty(GroupUser::whereGroupId($group->id)->whereUserId(auth()->user()->id)->first())) {
            return $this->ResponseError('Это не ваша группа');
        }

        $course = $group->course;
        $groupsSchoolDay = $group->groupsSchoolDay->mapWithKeys(function (GroupSchoolDay $item) {
            return [$item->course_school_day_id => [
                'id' => $item->id,
                'date' => $item->date,
                'order' => $item->order,
                'status' => $item->status,
            ]];
        });

        // расписание (все дни расписания, уроки в днях, дни группы со статусом)
        $courseSchoolDay = $course->course_school_days()->get(['id', 'order'])->map(function (CourseSchoolDay $item) {
            $lessons = $item->lessons()->orderBy('school_day_order')->get(['id', 'name', 'school_day_order', 'hours']);
            return [
                'id' => $item->id,
                'order' => $item->order,
                'lessons' => $lessons,
            ];
        });

        // учителя и ученики (список)
        $users = $group->groupUser()->get();
        $teachers = [];
        $disciples = [];
        foreach ($users as $user) {
            $member = $user->user()->get(['id', 'surname', 'name', 'patronymic'])->toArray();
            if($user->role == 'disciple'){
                $disciples[] = $member;
            }
            if($user->role == 'teacher'){
                $teachers[] = $member;
            }
        }

        // посещаемость (присутствовал/отсутствовал/?)
        $csd = $groupsSchoolDay->map(function ($item) {
            return $item['id'];
        })->toArray();


        $attendances = Attendance::whereUserId(auth()->user()->id)->get('group_school_day_id')
            ->filter(function ($item) use ($csd) {
                return in_array($item->group_school_day_id, $csd);
            })
            ->map(function (Attendance $item) {
                return $item->group_school_day_id;
            })
            ->toArray();

        // статус в группе (в процессе обучения / отчислен / переведен)

        return $this->ResponseOk([
            // группа (название)
            'group' => [
                'id' => $group->id,
                'name' => $group->name,
            ],
            // курс (название)
            'course' => [
                'id' => $course->id,
                'name' => $course->name,
            ],
            'courseSchoolDay' => $courseSchoolDay,
            'groupsSchoolDay' => $groupsSchoolDay,
            'attendances' => $attendances,
            'disciples' => $disciples,
            'teachers' => $teachers,
        ]);
    }
}
