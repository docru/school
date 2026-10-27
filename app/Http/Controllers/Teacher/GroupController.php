<?php

namespace App\Http\Controllers\Teacher;

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
        // Сводка та же, что у администратора: учителю нужны те же сведения о группе
        $groups = GroupUser::whereRole('teacher')
            ->whereUserId(auth()->id())
            ->with([
                'group.course:id,name',
                'group.course.course_school_days:id,course_id',
                'group.groupUser.user:id,name,surname,patronymic',
                'group.groupsSchoolDay',
            ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn(GroupUser $groupUser): ?array => $groupUser->group?->summary())
            ->filter()
            ->values()
            ->all();

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

        // расписание (все дни расписания, уроки в днях, дни группы со статусом)
        $courseSchoolDay = $course->course_school_days()->get(['id', 'order'])->map(function (CourseSchoolDay $item) {
            // homework_count — чтобы в списке уроков пометить те, где есть домашнее задание
            $lessons = $item->lessons()
                ->select(['id', 'name', 'school_day_order', 'hours'])
                ->withCount(['tasks as homework_count' => fn($q) => $q->where('kind', 'homework')])
                ->orderBy('school_day_order')
                ->get();
            return [
                'id' => $item->id,
                'order' => $item->order,
                'lessons' => $lessons,
            ];
        });

        // учителя и ученики (список). Телефон нужен, чтобы учитель мог позвонить,
        // статус — чтобы отчисленный не выглядел как остальные.
        $users = $group->groupUser()->with('user')->get();
        $teachers = [];
        $disciples = [];
        foreach ($users as $groupUser) {
            $user = $groupUser->user;
            if (empty($user)) {
                continue;
            }
            $member = [
                'id' => $user->id,
                'surname' => $user->surname,
                'name' => $user->name,
                'patronymic' => $user->patronymic,
                'phone' => $user->phone,
                'status' => $groupUser->status,
                'expelled_at' => $groupUser->expelled_at,
            ];
            if ($groupUser->role == 'disciple') {
                $disciples[] = $member;
            }
            if ($groupUser->role == 'teacher') {
                $teachers[] = $member;
            }
        }

        // посещаемость (присутствовал/отсутствовал/?)
        $gsd = $groupsSchoolDay->keys()->toArray();
        $attendances = Attendance::whereUserId(auth()->user()->id)->get('group_school_day_id')
            ->filter(function ($item) use ($gsd) {
                return in_array($item->group_school_day_id, $gsd);
            })
            ->map(function ($item) {
                return $item->group_school_day_id;
            })
            ->toArray();

        // отметки посещения по всей группе: «<id дня группы>_<id ученика>».
        // Нужны, чтобы учитель видел пропуски учеников так же, как администратор.
        $dayIds = $group->groupsSchoolDay->pluck('id')->toArray();
        $attendanceMarks = Attendance::whereIn('group_school_day_id', $dayIds)
            ->get(['group_school_day_id', 'user_id'])
            ->map(function (Attendance $item) {
                return $item->group_school_day_id . '_' . $item->user_id;
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
            'attendanceMarks' => $attendanceMarks,
            'disciples' => $disciples,
            'teachers' => $teachers,
        ]);
    }
}
