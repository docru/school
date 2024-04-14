<?php

use Illuminate\Support\Facades\Route;

Route::namespace('App\Http\Controllers')->group(function () {

    // авторизация по коду
    Route::get('/login/{entryCode}', 'UserController@login');

    Route::prefix('api')->group(function () {
        // профиль пользователя
        Route::group(['middleware' => ['auth']], function () {
            Route::get('/users/profile', 'UserController@profile');
            Route::get('/users/profile/{uid?}', 'UserController@profile');
            Route::post('/users/profileSave', 'UserController@profileSave');
            Route::get('/users/disciples/{groupId}', 'UserController@disciples');
        });

        // Суперадмин
        Route::group(['middleware' => ['role:superadmin']], function () {

            Route::get('/users', 'UserController@list'); // список всех пользователей
            Route::get('/users/roles', 'UserController@roles'); // список всех ролей с описанием (id, name, display_name, description)
            Route::post('/users/create', 'UserController@create'); // создать пользователя ($phone, $roles)
            Route::post('/users/auth-link', 'UserController@authLink'); // получить ссылку для пользователя ($uid)

            Route::prefix('superadmin')->group(function () {

            });
        });

        // методист
        Route::group(['middleware' => ['role:methodologist']], function () {
            Route::prefix('methodologist')->group(function () {
                Route::apiResources([
                    'courses' => \App\Http\Controllers\Methodologist\CourseController::class,
                    'lessons' => \App\Http\Controllers\Methodologist\LessonController::class,
                    'control' => \App\Http\Controllers\Methodologist\ControlController::class,
                    'tasks' => \App\Http\Controllers\Methodologist\TaskController::class,
                ]);
            });
        });

    // администратор
    Route::group(['middleware' => ['role:administrator']], function () {
        Route::get('/api/users', 'UserController@users');
        Route::get('/api/roles', 'UserController@roles');
        Route::post('/users/create', 'UserController@create');
        Route::post('/users/auth-link/{uid}', 'UserController@authLink');

            Route::prefix('administrator')->group(function () {

            });
        });

        // учитель
        Route::group(['middleware' => ['role:teacher']], function () {
            Route::prefix('teacher')->group(function () {

            });
        });

        // ученик
        Route::group(['middleware' => ['role:disciple']], function () {
            Route::prefix('disciple')->group(function () {

            });
        });
    });


    // страница подключения vue
    Route::fallback('UserController@index');


    if (class_exists(\App\Http\Controllers\AAController::class)){
        Route::get('/dev/', 'AAController@test');
    }

});
