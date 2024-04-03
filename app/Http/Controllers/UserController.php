<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Создать пользователя
     * @return void
     */
    public function create()
    {
        //
    }
    /**
     * Сгенерировать ссылку для авторизации
     * @return void
     */
    public function authLink($uid)
    {
        //
    }
    /**
     * Авторизация
     * @param $entryCode
     * @return void
     */
    public function login($entryCode)
    {
        //
    }

    /**
     * Список пользователей.
     */
    public function list()
    {
        //
    }

    /**
     * Список пользователей группы
     * @param $groupId
     * @return void
     */
    public function disciples($groupId)
    {
        //
    }

    /**
     * Профиль пользователя
     */
    public function profile($uid = false)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function profileSave(Request $request)
    {
        //
    }

}
