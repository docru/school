<?php

use App\Http\Controllers\Administrator\AttendanceController as AdministratorAttendanceController;
use App\Http\Controllers\Administrator\GroupController as AdministratorGroupController;
use App\Http\Controllers\Administrator\CourseController as AdministratorCourseController;
use App\Http\Controllers\Administrator\UserController as AdministratorUserController;
use App\Http\Controllers\Methodologist\ControlController as MethodologistControlController;
use App\Http\Controllers\Methodologist\CourseController as MethodologistCourseController;
use App\Http\Controllers\Methodologist\LessonController as MethodologistLessonController;
use App\Http\Controllers\Methodologist\ModuleController as MethodologistModuleController;
use App\Http\Controllers\Methodologist\TaskController as MethodologistTaskController;
use App\Http\Controllers\Teacher\GroupController as TeacherGroupController;
use App\Http\Controllers\Teacher\LessonController as TeacherLessonController;
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
            Route::get('/users/roles', 'UserController@roles'); // список всех ролей с описанием (id, name, display_name, description)
            Route::post('/users/create', 'UserController@create'); // создать пользователя ($phone, $roles)
        });

        Route::group(['middleware' => ['role:superadmin|administrator']], function () {
            Route::post('/users/auth-link/{uid}', 'UserController@authLink'); // получить ссылку для пользователя ($uid)
            Route::get('/users', 'UserController@list'); // список всех пользователей
            Route::get('/users/{role}', 'UserController@list'); // список всех пользователей с указанной ролью
        });

        // методист
        Route::group(['middleware' => ['role:methodologist']], function () {
            Route::prefix('methodologist')->namespace('Methodologist')->group(function () {
                Route::apiResources([
                    'courses' => MethodologistCourseController::class,
                    'modules' => MethodologistModuleController::class,
                    'lessons' => MethodologistLessonController::class,
                    'control' => MethodologistControlController::class,
                    'tasks' => MethodologistTaskController::class,
                ]);

                Route::post('course_school_day', [MethodologistCourseController::class, 'createCourseSchoolDay']);
                Route::delete('course_school_day/{courseSchoolDay}', [MethodologistCourseController::class, 'destroyCourseSchoolDay']);
            });
        });

        // администратор
        Route::group([
            'middleware' => ['role:administrator'],
            'prefix' => 'administrator',
            'namespace' => 'Administrator',
        ], function () {

            Route::apiResources([
                // курсы
                'courses' => AdministratorCourseController::class,
                // группы
                'groups' => AdministratorGroupController::class,
            ]);

            // ученики и учителя группы
            Route::controller(AdministratorUserController::class)->group(function () {
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
            Route::controller(AdministratorAttendanceController::class)->group(function () {
                Route::get('/attendance/{group}', 'index'); // посещение группы
                Route::post('/attendance/{groupSchoolDay}/set/{user}', 'set'); // отметить посещение ученика
            });
        });

        // учитель
        Route::group([
            'middleware' => ['role:teacher'],
            'prefix' => 'teacher',
//            'namespace' => 'Teacher',
        ], function () {
            // группы
            Route::apiResources([
                'groups' => TeacherGroupController::class,
            ]);
            // Урок
            Route::post('/lesson/{group}/{lesson}', [TeacherLessonController::class, 'show']);
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
    Route::fallback(function ($path = '')
    {
        if (empty(auth()->user())) {
            if (!empty($path)) {
                return redirect()->to('/');
            }
            return view('welcome');
        } else {
            return view('layout');
        }
    });


    if (class_exists(\App\Http\Controllers\AAController::class)) {
        Route::get('/dev/', 'AAController@test');
        Route::any('/convert/', 'AAController@convert');
    }

});
