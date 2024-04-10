<?php

use Illuminate\Support\Facades\Route;

Route::namespace('App\Http\Controllers')->group(function () {

    // авторизация по коду
    Route::get('/login/{entryCode}', 'UserController@login');

    // профиль пользователя
    Route::group(['middleware' => ['auth']], function () {
//        Route::get('/users/profile', 'UserController@profile'); @TODO получть профиль авторизованного пользователя с ролью
        Route::get('/users/profile/{uid?}', 'UserController@profile');
        Route::post('/users/profileSave', 'UserController@profileSave');
        Route::get('/users/disciples/{groupId}', 'UserController@disciples');
    });

    // Суперадмин
    Route::group(['middleware' => ['role:superadmin']], function () {

//        Route::get('/users', 'UserController@users'); @TODO получить всех юзеров? редакировать/удалять  ...
        Route::post('/users/create', 'UserController@create');
        Route::post('/users/auth-link', 'UserController@authLink');

        Route::prefix('superadmin')->group(function () {

        });
    });

    // методист
    Route::group(['middleware' => ['role:methodologist']], function () {
        Route::prefix('methodologist')->group(function () {
            Route::resources([
                'lessons' => \App\Http\Controllers\Methodologist\LessonController::class,
                'courses' => \App\Http\Controllers\Methodologist\CourseController::class,
                'control' => \App\Http\Controllers\Methodologist\ControlController::class,
                'tasks' => \App\Http\Controllers\Methodologist\TaskController::class,
            ]);
        });
    });

    // администратор
    Route::group(['middleware' => ['role:administrator']], function () {
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


    // страница подключения vue
    Route::fallback('UserController@index');
});
