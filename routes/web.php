<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/profile', 'ProfileController@userProfile');

// ученик
Route::prefix('disciple')->group(function () {

});

// учитель
Route::prefix('teacher')->group(function () {

});

// администратор
Route::prefix('administrator')->group(function () {

});

// методист
Route::prefix('methodologist')->group(function () {
    Route::resources([
        'lessons' => \App\Http\Controllers\Methodologist\LessonController::class,
        'courses' => \App\Http\Controllers\Methodologist\CourseController::class,
        'control' => \App\Http\Controllers\Methodologist\ControlController::class,
        'tasks' => \App\Http\Controllers\Methodologist\TaskController::class,
    ]);
});





//    Route::resources([
//        'groups' => \App\Http\Controllers\GroupController::class,
//        'lessons' => \App\Http\Controllers\LessonController::class,
//        'courses' => \App\Http\Controllers\CourseController::class,
//        'control' => \App\Http\Controllers\ControlController::class,
//        'tasks' => \App\Http\Controllers\TaskController::class,
//        'answers' => \App\Http\Controllers\AnswerController::class,
//        'school_days' => \App\Http\Controllers\SchoolDayController::class,
//        'comments' => \App\Http\Controllers\CommentController::class,
//    ]);
