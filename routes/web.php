<?php

use App\Http\Controllers\Administrator\AttendanceController;
use App\Http\Controllers\Administrator\GroupController as AdministratorGroupController;
use App\Http\Controllers\Administrator\GroupSchoolDayController;
use App\Http\Controllers\Administrator\UserController;
use App\Http\Controllers\Methodologist\ControlController;
use App\Http\Controllers\Methodologist\CourseController;
use App\Http\Controllers\Methodologist\LessonController;
use App\Http\Controllers\Methodologist\ModuleController;
use App\Http\Controllers\Methodologist\TaskController;
use App\Http\Controllers\Disciple\GroupController as DiscipleGroupController;
use App\Http\Controllers\Disciple\LessonController as DiscipleLessonController;
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
        Route::group([
            'middleware' => ['role:administrator'],
            'prefix' => 'administrator',
            'namespace' => 'Administrator',
        ], function () {

            // группы
            Route::apiResources([
                'groups' => AdministratorGroupController::class,
            ]);

            // ученики и учителя группы
            Route::controller(UserController::class)->group(function () {
                Route::get('/group/{group}/users', 'index'); // пользователи группы
                Route::post('/group/{group}/join-user/{user}/{role}', 'joinUserToGroup'); // зачислить юзера в группу
                Route::post('/group/{group}/remove-user/{user}', 'removeUserFromGroup'); // удалить юзера из группы
            });

            // Добавить новый учебный день группы
            Route::controller(AdministratorGroupController::class)->group(function () {
                Route::post('/groups/school-day/{group}/add', 'addGroupsSchoolDay');
                Route::post('/groups/school-day/{group}/close', 'closeGroupsSchoolDay');
            });

            // посещение
            Route::controller(AttendanceController::class)->group(function () {
                Route::get('/attendance/{group}', 'index'); // посещение группы
                Route::post('/attendance/{groupSchoolDay}/set/{user}', 'set'); // отметить посещение ученика
            });
        });

        // учитель
        Route::group(['middleware' => ['role:teacher']], function () {
            Route::prefix('teacher')->namespace('Teacher')->group(function () {

            });
        });

        // ученик
        Route::group([
            'middleware' => ['role:disciple'],
            'prefix' => 'disciple',
//            'namespace' => 'Disciple',
        ], function () {
            // группы
            Route::apiResources([
                'groups' => DiscipleGroupController::class,
            ]);
            // Урок
            Route::post('/lesson/{group}/{lesson}', [DiscipleLessonController::class, 'show']);
        });
    });


    // страница подключения vue
    Route::fallback('UserController@index');


    if (class_exists(\App\Http\Controllers\AAController::class)) {
        Route::get('/dev/', 'AAController@test');
    }

});
