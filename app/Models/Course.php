<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 *
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string $name Название курса
 * @property string $description Описание курса
 * @method static Builder|Course newModelQuery()
 * @method static Builder|Course newQuery()
 * @method static Builder|Course query()
 * @method static Builder|Course whereCalendarPlan($value)
 * @method static Builder|Course whereCreatedAt($value)
 * @method static Builder|Course whereDescription($value)
 * @method static Builder|Course whereId($value)
 * @method static Builder|Course whereName($value)
 * @method static Builder|Course whereUpdatedAt($value)
 * @method static Builder|Course whereDeletedAt($value)
 * @property-read Collection<int, \App\Models\Lesson> $lessons
 * @property-read int|null $lessons_count
 * @property-read Collection<int, \App\Models\Module> $modules
 * @property-read int|null $modules_count
 * @property-read Collection<int, \App\Models\CourseSchoolDay> $course_school_days
 * @property-read int|null $course_school_days_count
 * @mixin \Eloquent
 */
class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description'
    ];

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    public function course_school_days()
    {
        return $this->hasMany(CourseSchoolDay::class);
    }

    public function dump()
    {
        return [
            'course' => $this->toArray(),
            'studyProgram' => $this->studyProgram(),
            'schedule' => $this->schedule()
        ];
    }

    public function studyProgram()
    {
        $moduleKeys = ['id', 'name', 'order'];
        $lessonsKeys = [
            'id',
            'name',
            'hours',
            'methodical_description',
            'abstract',
            'school_day',
            'school_day_order',
            'order',
        ];

        return $this->modules()->orderBy('order')->get()->map(function ($module) use ($moduleKeys, $lessonsKeys) {
            return [
                'module' => $module->only($moduleKeys),
                'lessons' => $module->lessons()
                    ->orderBy('order')
                    ->get($lessonsKeys)
                    ->toArray(),
            ];
        })->toArray();
    }

    public function saveStudyProgram(array $saveStudyProgram)
    {
        foreach ($saveStudyProgram as $itemModule) {
            $saveModule = $itemModule['module'];
            $module = $this->modules()->find($saveModule['id']);
            if (!empty($module)) {
                $module->update($saveModule);
            }

            $saveLessons = $itemModule['lessons'];
            foreach ($saveLessons as $order => $saveLesson) {
                $saveLesson['order'] = $order + 1;
                $saveLesson['module_id'] = $saveModule['id'];
                $lesson = $this->lessons()->find($saveLesson['id']);
                if (!empty($lesson)) {
                    $lesson->update($saveLesson);
                }
            }
        }
    }

    public function schedule()
    {
        return $this->course_school_days()->orderBy('order')->get()
            ->map(function (CourseSchoolDay $day) {
                return [
                    'id' => $day->id,
                    'order' => $day->order,
                ];
            });

    }
}
