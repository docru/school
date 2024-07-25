<?php

namespace App\Http\Controllers\Methodologist;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseSchoolDay;
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
        return $this->ResponseOk($course->dump());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        $course->update($request->input('course'));

        $res = file_get_contents('php://input');
        $req = json_decode($res, 1);

        $course->saveStudyProgram($req['studyProgram']);
//        $course->saveStudyProgram($request->input('studyProgram'));

        return $this->ResponseOk($course->dump());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        //
    }

    public function createCourseSchoolDay(Request $request)
    {
        $course = Course::whereId($request->input('course_id'))->first();
        if (empty($course)) {
            return $this->ResponseError('Нет курса');
        }

        $course_school_day = new CourseSchoolDay();
        $course_school_day->course_id = $course->id;
        $course_school_day->order = $course->course_school_days->count() + 1;
        $course_school_day->save();

        return $this->ResponseOk($course->dump());
    }

    public function destroyCourseSchoolDay(CourseSchoolDay $courseSchoolDay)
    {
        $course = $courseSchoolDay->course;
        $courseSchoolDay->delete();

        $courseSchoolDays = $course->course_school_days()->orderBy('order')->get();
        foreach ($courseSchoolDays as $k => $courseSchoolDay) {
            $courseSchoolDay->order = $k + 1;
            $courseSchoolDay->save();
        }

        return $this->ResponseOk($course->dump());
    }
}
