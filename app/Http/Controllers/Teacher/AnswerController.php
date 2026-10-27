<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Group;
use App\Models\GroupUser;
use Illuminate\Http\Request;

/**
 * Проверка ответов учеников: список по группе, без захода в каждого.
 * Показываются только вопросы — остальные блоки ответа не требуют.
 */
class AnswerController extends Controller
{
    /**
     * GET /teacher/answers?group_id=10&status=submitted
     */
    public function index(Request $request)
    {
        $group = Group::find($request->input('group_id'));
        if (empty($group)) {
            return $this->ResponseError('Не указана группа');
        }
        if (!$this->teachesGroup($group->id)) {
            return $this->ResponseError('Это не ваша группа');
        }

        $query = Answer::whereGroupId($group->id)
            ->whereHas('task', fn($q) => $q->where('type', 'question'))
            ->whereIn('status', [Answer::STATUS_SUBMITTED, Answer::STATUS_CHECKED])
            ->with(['task.lesson:id,name,order', 'user:id,surname,name,patronymic']);

        $status = $request->input('status');
        if (in_array($status, [Answer::STATUS_SUBMITTED, Answer::STATUS_CHECKED])) {
            $query->whereStatus($status);
        }

        $answers = $query->get()->map(fn(Answer $answer): array => [
            'id' => $answer->id,
            'status' => $answer->status,
            'answer' => $answer->answer,
            'comment' => $answer->comment,
            'submitted_at' => $answer->submitted_at?->format('Y-m-d H:i'),
            'checked_at' => $answer->checked_at?->format('Y-m-d H:i'),
            'user_id' => $answer->user_id,
            'user_name' => trim(($answer->user?->surname ?? '') . ' ' . ($answer->user?->name ?? '')),
            'lesson_id' => $answer->task?->lesson_id,
            'lesson_name' => $answer->task?->lesson?->name ?? '',
            'question' => $answer->task?->title ?? '',
            'question_body' => $answer->task?->body,
        ])->values();

        // Непроверенные сверху, внутри — свежие первыми
        $sorted = $answers->sortBy([
            fn(array $a, array $b): int => ($a['status'] === Answer::STATUS_CHECKED ? 1 : 0) <=> ($b['status'] === Answer::STATUS_CHECKED ? 1 : 0),
            fn(array $a, array $b): int => strcmp((string)$b['submitted_at'], (string)$a['submitted_at']),
        ])->values()->toArray();

        return $this->ResponseOk($sorted);
    }

    /**
     * Отметить ответ проверенным и оставить комментарий.
     * POST /teacher/answers/{answer}/check
     */
    public function check(Request $request, Answer $answer)
    {
        if (!$this->teachesGroup($answer->group_id)) {
            return $this->ResponseError('Это не ваша группа');
        }
        if ($answer->status === Answer::STATUS_DRAFT) {
            return $this->ResponseError('Ответ ещё не сдан');
        }

        $comment = trim(strip_tags((string)$request->input('comment')));
        $checked = (bool)$request->input('checked', true);

        $answer->comment = $comment ?: null;
        $answer->status = $checked ? Answer::STATUS_CHECKED : Answer::STATUS_SUBMITTED;
        $answer->checked_at = $checked ? now() : null;
        $answer->checked_by = $checked ? auth()->id() : null;
        $answer->save();

        return $this->ResponseOk(['msgOk' => $checked ? 'Отмечено проверенным' : 'Отметка снята']);
    }

    private function teachesGroup($groupId): bool
    {
        return !empty($groupId)
            && !empty(GroupUser::whereGroupId($groupId)
                ->whereUserId(auth()->id())
                ->whereRole('teacher')
                ->first());
    }
}
