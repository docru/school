<?php

namespace App\Http\Controllers\Disciple;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(Group $group, Lesson $lesson)
    {
        if ($group->course_id != $lesson->course_id) {
            return $this->ResponseError('Нет такого урока');
        }

        if (empty(GroupUser::whereGroupId($group->id)->whereUserId(auth()->user()->id)->first())) {
            return $this->ResponseError('Это не ваша группа');
        }

        return $this->ResponseOk([
            'lesson' => [
                'id' => $lesson->id,
                'name' => $lesson->name,
                'hours' => $lesson->hours,
                'abstract' => $lesson->abstract,
            ],
        ]);
    }

}
