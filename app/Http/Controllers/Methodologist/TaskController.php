<?php

namespace App\Http\Controllers\Methodologist;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Task;
use Illuminate\Http\Request;

/**
 * Блоки заданий урока: домашнее задание и задания на уроке.
 * Различает их kind, устройство одинаковое.
 */
class TaskController extends Controller
{
    /**
     * Блоки урока нужного вида, по порядку.
     * GET /methodologist/tasks?lesson_id=1&kind=homework
     */
    public function index(Request $request)
    {
        $lessonId = $request->input('lesson_id');
        $kind = $request->input('kind', 'homework');

        if (empty($lessonId)) {
            return $this->ResponseError('Не указан урок');
        }
        if (!in_array($kind, Task::KINDS)) {
            return $this->ResponseError("Неизвестный вид заданий '$kind'");
        }

        return $this->ResponseOk($this->listTasks($lessonId, $kind));
    }

    /**
     * Создать блок. Порядок — следующий за последним.
     */
    public function store(Request $request)
    {
        $lessonId = $request->input('lesson_id');
        $kind = $request->input('kind', 'homework');
        $type = $request->input('type');

        if (empty(Lesson::find($lessonId))) {
            return $this->ResponseError('Нет такого урока');
        }
        if (!in_array($kind, Task::KINDS)) {
            return $this->ResponseError("Неизвестный вид заданий '$kind'");
        }
        if (!in_array($type, Task::TYPES)) {
            return $this->ResponseError("Неизвестный тип блока '$type'");
        }

        $last = Task::whereLessonId($lessonId)->whereKind($kind)->max('order');

        Task::create([
            'lesson_id' => $lessonId,
            'kind' => $kind,
            'type' => $type,
            'order' => ($last ?? 0) + 1,
            'title' => trim(strip_tags($request->input('title', ''))) ?: $this->defaultTitle($type),
            'body' => $request->input('body'),
            'url' => $request->input('url'),
        ]);

        return $this->ResponseOk($this->listTasks($lessonId, $kind));
    }

    /**
     * Сохранить блок. Тип и урок не меняются.
     */
    public function update(Request $request, Task $task)
    {
        $title = trim(strip_tags($request->input('title', '')));
        $task->update([
            'title' => $title ?: $this->defaultTitle($task->type),
            'body' => $request->input('body'),
            'url' => $request->input('url'),
        ]);

        return $this->ResponseOk($this->listTasks($task->lesson_id, $task->kind));
    }

    public function destroy(Task $task)
    {
        $lessonId = $task->lesson_id;
        $kind = $task->kind;
        $task->delete();
        $this->renumber($lessonId, $kind);

        return $this->ResponseOk($this->listTasks($lessonId, $kind));
    }

    /**
     * Новый порядок блоков: приходит список id в нужной последовательности.
     * POST /methodologist/tasks/reorder
     */
    public function reorder(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return $this->ResponseError('Не передан порядок блоков');
        }

        $tasks = Task::whereIn('id', $ids)->get()->keyBy('id');
        $first = $tasks->first();
        if (empty($first)) {
            return $this->ResponseError('Блоки не найдены');
        }

        foreach (array_values($ids) as $position => $id) {
            $task = $tasks->get($id);
            // Переносить блоки между уроками и видами нельзя
            if (!empty($task) && $task->lesson_id === $first->lesson_id && $task->kind === $first->kind) {
                $task->order = $position + 1;
                $task->save();
            }
        }

        return $this->ResponseOk($this->listTasks($first->lesson_id, $first->kind));
    }

    private function listTasks(int $lessonId, string $kind): array
    {
        return Task::whereLessonId($lessonId)
            ->whereKind($kind)
            ->orderBy('order')
            ->get(['id', 'lesson_id', 'kind', 'type', 'order', 'title', 'body', 'url'])
            ->toArray();
    }

    /** После удаления нумерация не должна зиять дырами */
    private function renumber(int $lessonId, string $kind): void
    {
        Task::whereLessonId($lessonId)
            ->whereKind($kind)
            ->orderBy('order')
            ->get()
            ->each(function (Task $task, int $i) {
                $task->order = $i + 1;
                $task->save();
            });
    }

    private function defaultTitle(string $type): string
    {
        return match ($type) {
            'memorize' => 'Выучить наизусть',
            'question' => 'Вопрос',
            'video' => 'Посмотреть видео',
            default => 'Без названия',
        };
    }
}
