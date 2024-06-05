<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Group;
use App\Models\GroupSchoolDay;
use App\Models\User;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Group $group)
    {
        $res = [];
        $group->groupsSchoolDay->each(function ($item) use (&$res) {
            foreach ($item->attendances as $attendance) {
                $res[] = $attendance->group_school_day_id . '_' . $attendance->user_id;
            }
        });
        return $this->ResponseOk($res);
    }

    /**
     * Удалить посещение ученика.
     */
    public function set(GroupSchoolDay $groupSchoolDay, User $user)
    {
        $attendance = Attendance::whereUserId($user->id)->whereGroupSchoolDayId($groupSchoolDay->id)->first();
        if (empty($attendance)) {
            $attendance = new Attendance();
            $attendance->user_id = $user->id;
            $attendance->group_school_day_id = $groupSchoolDay->id;
            $attendance->save();
            $group = $groupSchoolDay->group;
        } else {
            $group = $groupSchoolDay->group;
            $attendance->delete();
        }
        return $this->index($group);
    }

}
