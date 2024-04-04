<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Создать пользователя
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index($path = '')
    {
        if (empty(auth()->user())) {
            if (!empty($path)) {
                return redirect()->to('/');
            }
            return view('welcome');
        } else {
            return view('layout');
        }
    }

    /**
     * Авторизация
     * @param $entryCode
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login($entryCode)
    {
        // TODO - чистить устаревшие коды входа
        $user = User::whereEntryCode($entryCode)->first();
        if (!empty($user)) {
            Auth::login($user);
            // TODO - чистить код входа
//            $user->entry_code = null;
//            $user->save();
        }
        return redirect()->to('/');
    }

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
     * @return string
     */
    public function authLink($uid)
    {
        // Проверитиь права на генерацию ссылки
        if (empty(auth()->user()) || !auth()->user()->hasRole(['owner', 'admin'])) {
            return '';
        }
        $user = User::whereId($uid)->first();
        if (empty($user)) {
            return 'Не такого пользователя';
        }

        $user->entry_code = \Str::random();
        $user->entry_code_generated_at = date('Y-m-d H:i:s');
        $user->save();
        return env('APP_URL') . '/login/' . $user->entry_code;
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
