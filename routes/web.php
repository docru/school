<?php

use App\Http\Controllers\Administrator\AttendanceController;
use App\Http\Controllers\Administrator\GroupController;
use App\Http\Controllers\Administrator\GroupSchoolDayController;
use App\Http\Controllers\Administrator\UserController;
use App\Http\Controllers\Methodologist\ControlController;
use App\Http\Controllers\Methodologist\CourseController;
use App\Http\Controllers\Methodologist\LessonController;
use App\Http\Controllers\Methodologist\ModuleController;
use App\Http\Controllers\Methodologist\TaskController;
use Illuminate\Support\Facades\Route;

Route::namespace('App\Http\Controllers')->group(function () {

    // авторизация по коду
    Route::get('/login/{entryCode}', 'UserController@login');

    Route::prefix('api')->group(function () {
        // профиль пользователя
        Route::group(['middleware' => ['auth']], function () {
            Route::get('/users/profile', 'UserController@profile');
            Route::get('/users/profile/{uid?}', 'UserController@profile');
            Route::post('/users/profile', 'UserController@profileSave');
        });

        // Суперадмин
        Route::group(['middleware' => ['role:superadmin']], function () {

            Route::get('/users', 'UserController@list'); // список всех пользователей
            Route::get('/users/roles', 'UserController@roles'); // список всех ролей с описанием (id, name, display_name, description)
            Route::post('/users/create', 'UserController@create'); // создать пользователя ($phone, $roles)
            Route::post('/users/auth-link/{uid}', 'UserController@authLink'); // получить ссылку для пользователя ($uid)

            Route::prefix('superadmin')->group(function () {

            });
        });

        // методист
        Route::group(['middleware' => ['role:methodologist']], function () {
            Route::prefix('methodologist')->namespace('Methodologist')->group(function () {
                Route::apiResources([
                    'courses' => CourseController::class,
                    'modules' => ModuleController::class,
                    'lessons' => LessonController::class,
                    'control' => ControlController::class,
                    'tasks' => TaskController::class,
                ]);

                Route::post('course_school_day', [CourseController::class, 'createCourseSchoolDay']);
                Route::delete('course_school_day/{courseSchoolDay}', [CourseController::class, 'destroyCourseSchoolDay']);
            });
        });

        // администратор
        Route::group(['middleware' => ['role:administrator']], function () {
            Route::prefix('administrator')->namespace('Administrator')->group(function () {
                Route::post('/groups/join-user', 'GroupController@joinUserToGroup'); // зачислить ученика в группу
                Route::post('/groups/school-day/{group}', 'GroupController@addGroupsSchoolDay'); // Добавить новый учебный день группы
                Route::apiResources([
                    'disciples' => UserController::class,
                    'groups' => GroupController::class,
                    'groupsSchoolDay' => GroupSchoolDayController::class,
                    'attendance' => AttendanceController::class,
                ]);
                Route::get('/groups/users/{group}', 'GroupController@users'); // зачислить ученика в группу
            });
            Route::post('/users/auth-link/{uid}', 'UserController@authLink'); // получить ссылку для пользователя ($uid)
        });

        // учитель
        Route::group(['middleware' => ['role:teacher']], function () {
            Route::prefix('teacher')->namespace('Teacher')->group(function () {

            });
        });

        // ученик
        Route::group(['middleware' => ['role:disciple']], function () {
            Route::prefix('disciple')->namespace('Disciple')->group(function () {

            });
        });
    });


    // страница подключения vue
    Route::fallback('UserController@index');


    if (class_exists(\App\Http\Controllers\AAController::class)) {
        Route::get('/dev/', 'AAController@test');
    }

});
