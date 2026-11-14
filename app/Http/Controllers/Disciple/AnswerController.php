<?php

namespace App\Http\Controllers\Disciple;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\GroupUser;
use App\Models\Lesson;
use App\Models\Task;
use Illuminate\Http\Request;

/**
 * Ответы ученика на задания урока.
 *
 * Набранное сохраняется черновиком по ходу работы, «Сдать» переводит
 * ответы урока в submitted — после этого правка закрыта.
 */
class AnswerController extends Controller
{
    /**
     * Сохранить черновик ответа.
     * POST /disciple/answers
     */
    public function store(Request $request)
    {
        $task = Task::find($request->input('task_id'));
        if (empty($task)) {
            return $this->ResponseError('Нет такого задания');
        }
        if (!$task->isAnswerable()) {
            return $this->ResponseError('На это задание ответ не предусмотрен');
        }

        $groupId = $request->input('group_id');
        if (!$this->inGroup($groupId)) {
            return $this->ResponseError('Это не ваша группа');
        }

        $answer = Answer::firstOrNew([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
        ]);

        // Проверенное и сданное ученик больше не правит
        if (in_array($answer->status, [Answer::STATUS_SUBMITTED, Answer::STATUS_CHECKED])) {
            return $this->ResponseError('Ответ уже сдан, изменить его нельзя');
        }

        $answer->group_id = $groupId;
        $answer->answer = $request->input('answer');
        $answer->status = Answer::STATUS_DRAFT;
        $answer->save();

        return $this->ResponseOk(['id' => $answer->id, 'status' => $answer->status]);
    }

    /**
     * Сдать задание урока на проверку.
     * POST /disciple/answers/submit
     */
    public function submit(Request $request)
    {
        $lesson = Lesson::find($request->input('lesson_id'));
        if (empty($lesson)) {
            return $this->ResponseError('Нет такого урока');
        }

        $groupId = $request->input('group_id');
        if (!$this->inGroup($groupId)) {
            return $this->ResponseError('Это не ваша группа');
        }

        $kind = $request->input('kind', 'homework');
        if (!in_array($kind, Task::KINDS)) {
            return $this->ResponseError("Неизвестный вид заданий '$kind'");
        }

        $taskIds = $lesson->tasks()->whereKind($kind)->whereType('question')->pluck('id');
        if ($taskIds->isEmpty()) {
            return $this->ResponseError('В задании нет вопросов');
        }

        $answers = Answer::whereUserId(auth()->id())
            ->whereIn('task_id', $taskIds)
            ->whereNotNull('answer')
            ->where('answer', '!=', '')
            ->get();

        if ($answers->count() < $taskIds->count()) {
            return $this->ResponseError('Сначала ответьте на все вопросы');
        }

        foreach ($answers as $answer) {
            if ($answer->status === Answer::STATUS_DRAFT) {
                $answer->status = Answer::STATUS_SUBMITTED;
                $answer->submitted_at = now();
                $answer->save();
            }
        }

        return $this->ResponseOk(['msgOk' => 'Задание сдано на проверку']);
    }

    private function inGroup($groupId): bool
    {
        return !empty($groupId)
            && !empty(GroupUser::whereGroupId($groupId)->whereUserId(auth()->id())->first());
    }
}
