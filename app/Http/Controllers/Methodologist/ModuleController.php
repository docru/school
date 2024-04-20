<?php

namespace App\Http\Controllers\Methodologist;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $courseId = (int)$request->input('courseId');
        $moduleName = trim($request->input('moduleName'));
        if (empty($moduleName)) {
            return $this->ResponseError('Нужно задать название модуля');
        }
        if (!empty(Module::whereCourseId($courseId)->whereName($moduleName)->first())) {
            return $this->ResponseError('С таким названием в этом курсе уже есть модуль');
        }

        $module = new Module(['name' => $moduleName, 'course_id' => $courseId]);
        $module->save();

        return $this->ResponseOk(Course::whereId($courseId)->first()->studyProgram());
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Module $module)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Module $module)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Module $module)
    {
        if (empty($module)) {
            return $this->ResponseError('Нет модуля');
        }
        if ($module->lessons()->count() > 0) {
            return $this->ResponseError('Нельзя удалить модуль с уроками');
        }

        $course = $module->course;

        $module->delete();

        return $this->ResponseOk($course->studyProgram());
    }
}
