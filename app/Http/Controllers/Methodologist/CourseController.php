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
        return $this->ResponseOk(Course::all(['id', 'name'])->toArray());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $nameCourse = $request->input('nameCourse');
        if(empty($nameCourse)){
            return $this->ResponseError('Не задано название курса');
        }
        if(!empty(Course::whereName($nameCourse)->first())){
            return $this->ResponseError('С таким названием курс уже есть');
        }

        $course = new Course(['name'=>$nameCourse]);
        $course->save();
        return $this->ResponseOk(Course::all(['id', 'name'])->toArray());
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        return $this->ResponseOk($course->toArray());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        //
    }
}
