<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Course;

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
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        return $this->ResponseOk($course->dump());
    }
}
