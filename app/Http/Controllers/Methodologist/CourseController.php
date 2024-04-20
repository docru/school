<?php

namespace App\Http\Controllers\Methodologist;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->ResponseOk(Course::all(['id', 'name', 'description'])->toArray());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $nameCourse = $request->input('nameCourse');
        $descriptionCourse = $request->input('description');
        if (empty($nameCourse)) {
            return $this->ResponseError('Не задано название курса');
        }
        if (!empty(Course::whereName($nameCourse)->first())) {
            return $this->ResponseError('С таким названием курс уже есть');
        }

        $course = new Course(['name' => $nameCourse, 'description' => $descriptionCourse]);
        $course->save();
        return $this->ResponseOk(Course::all(['id', 'name', 'description'])->toArray());
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        return $this->ResponseOk(['course' => $course->toArray(), 'studyProgram' => $course->studyProgram()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        if (empty($course)) {
            return $this->ResponseError('Нет курса');
        }

        $course->update($request->input('course'));

        $course->saveStudyProgram($request->input('studyProgram'));

        return $this->ResponseOk($course->studyProgram());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        //
    }
}
