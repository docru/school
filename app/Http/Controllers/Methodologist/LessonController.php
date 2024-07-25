<?php

namespace App\Http\Controllers\Methodologist;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $courseId = $request->input('courseId');
        $moduleId = $request->input('moduleId');
        $lessonName = $request->input('lessonName');
        if (empty($lessonName)) {
            return $this->ResponseError('Не задано название Урока');
        }
        if (empty(Course::whereId($courseId)->first())) {
            return $this->ResponseError('Нет курса');
        }
        if (!!$moduleId) {
            $module = Module::whereCourseId($courseId)->whereId($moduleId)->first();
            if (empty($module)) {
                return $this->ResponseError('Нет модуля');
            }
        }


        $lesson = new Lesson();
        $lesson->course_id = $courseId;
        $lesson->name = $lessonName;
        $lesson->methodical_description = '';
        $lesson->abstract = '';
        if (!empty($moduleId)) {
            $lesson->module_id = $moduleId;

        }
        $lesson->save();

        return $this->ResponseOk(Course::whereId($courseId)->first()->dump());
    }

    /**
     * Display the specified resource.
     */
    public function show(Lesson $lesson)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lesson $lesson)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lesson $lesson)
    {
        if (empty($lesson)) {
            return $this->ResponseError('Нет урока');
        }

        $course = $lesson->course;

        $lesson->delete();

        return $this->ResponseOk($course->dump());
    }
}
