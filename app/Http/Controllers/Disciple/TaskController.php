<?php

namespace App\Http\Controllers\Disciple;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\GroupSchoolDay;
use App\Models\GroupUser;
use App\Models\Lesson;
use App\Models\Task;

/**
 * Домашние задания ученика списком: по всем его группам.
 */
class TaskController extends Controller
{
    /**
     * GET /disciple/tasks
     */
    public function index()
    {
        $groupUsers = GroupUser::whereRole('disciple')
            ->whereUserId(auth()->id())
            ->with('group.course')
            ->get();

        $items = [];
        foreach ($groupUsers as $groupUser) {
            $group = $groupUser->group;
            if (empty($group) || empty($group->course)) {
                continue;
            }

            // Домашнее задание открывается после проведённого занятия
            $closedDays = GroupSchoolDay::whereGroupId($group->id)
                ->whereStatus('close')
                ->pluck('date', 'course_school_day_id');

            if ($closedDays->isEmpty()) {
                continue;
            }

            $lessons = Lesson::whereCourseId($group->course_id)
                ->whereIn('school_day', $closedDays->keys())
                ->orderBy('order')
                ->get(['id', 'name', 'school_day', 'order']);

            foreach ($lessons as $lesson) {
                $tasks = Task::whereLessonId($lesson->id)->whereKind('homework')->get(['id', 'type']);
                if ($tasks->isEmpty()) {
                    continue;
                }

                $questionIds = $tasks->where('type', 'question')->pluck('id');
                $answers = Answer::whereUserId(auth()->id())->whereIn('task_id', $questionIds)->get();

                $items[] = [
                    'lesson_id' => $lesson->id,
                    'lesson_name' => $lesson->name,
                    'group_id' => $group->id,
                    'group_name' => $group->name,
                    'course_name' => $group->course->name,
                    'date' => $closedDays[$lesson->school_day] ?? null,
                    'blocks' => $tasks->count(),
                    'questions' => $questionIds->count(),
                    'answered' => $answers->filter(fn(Answer $a): bool => !empty($a->answer))->count(),
                    'status' => $this->status($questionIds->count(), $answers),
                ];
            }
        }

        // Свежие занятия сверху
        usort($items, fn(array $a, array $b): int => strcmp((string)$b['date'], (string)$a['date']));

        return $this->ResponseOk($items);
    }

    /**
     * Состояние задания целиком: по вопросам урока.
     */
    private function status(int $questions, $answers): string
    {
        if ($questions === 0) {
            return 'noQuestions';
        }
        if ($answers->isEmpty()) {
            return 'new';
        }
        if ($answers->where('status', Answer::STATUS_CHECKED)->count() === $questions) {
            return 'checked';
        }
        if ($answers->whereIn('status', [Answer::STATUS_SUBMITTED, Answer::STATUS_CHECKED])->count() === $questions) {
            return 'submitted';
        }
        return 'draft';
    }
}
