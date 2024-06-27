<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Lesson;

class LessonController extends Controller
{
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
                'methodical_description' => $lesson->methodical_description,
            ],
        ]);
    }
}
