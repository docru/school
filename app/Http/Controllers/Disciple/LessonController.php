<?php

namespace App\Http\Controllers\Disciple;

use App\Http\Controllers\Controller;
use App\Models\Answer;
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
            'tasks' => $this->tasks($lesson),
            'answers' => $this->answers($lesson),
        ]);
    }

    /** Блоки урока по видам, в заданном методистом порядке */
    private function tasks(Lesson $lesson): array
    {
        $all = $lesson->tasks()->get(['id', 'kind', 'type', 'order', 'title', 'body', 'url']);
        return [
            'classwork' => $all->where('kind', 'classwork')->values()->toArray(),
            'homework' => $all->where('kind', 'homework')->values()->toArray(),
        ];
    }

    /**
     * Ответы ученика на задания этого урока: {task_id: {...}}.
     * Комментарий учителя приходит сюда же — ученик должен его видеть.
     */
    private function answers(Lesson $lesson): array
    {
        return Answer::whereUserId(auth()->id())
            ->whereIn('task_id', $lesson->tasks()->pluck('id'))
            ->get()
            ->mapWithKeys(fn(Answer $answer): array => [$answer->task_id => [
                'id' => $answer->id,
                'answer' => $answer->answer,
                'status' => $answer->status,
                'comment' => $answer->comment,
                'checked_at' => $answer->checked_at?->format('Y-m-d H:i'),
            ]])
            ->toArray();
    }
}
