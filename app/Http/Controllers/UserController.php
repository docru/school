<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RestController;
use App\Models\Lesson;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use mysql_xdevapi\Exception;


class UserController extends RestController
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

		$phone = User::phoneNormalize(request()->post('phone'));
		if (!$phone) {
			return $this->ResponseError('Не верный формат телефона');
		}
		$user = User::wherePhone($phone)->first();
		if (!empty($user)) {
			return $this->ResponseError('Пользователь с таким телефоном уже зарегистрирован');
		}
		$user = User::create(['phone' => $phone]);

		$roles = request()->post('roles');


		$existsRoles = Role::all()->map(function (Role $role) {
			return $role->name;
		})->toArray();

		$roles = array_intersect($roles, $existsRoles);
		$user->addRoles($roles);
		return $this->list();
//			return $this->ResponseOk($res);


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
		$users = User::all()->sortBy('id')->map(function (User $user) {
			return [
				'id' => $user->id,
				'phone' => $user->phone,
				'name' => $user->name,
				'nickname' => $user->nickname,
			];
		});

		return $this->ResponseOk(['users' => $users]);
	}

	public function roles()
	{
		return $this->ResponseOk(['roles' => Role::all()->map(function (Role $role) {
			return [
				'id' => $role->id,
				'name' => $role->name,
				'display_name' => $role->display_name,
				'description' => $role->description,
			];
		})]);
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
		if ($uid) {
			$user = User::whereId($uid)->first();
		} else {
			$user = auth()->user();
		}
		$roles = $user->roles->map(function ($item) {
			return $item->name;
		})->toArray();

		return $this->ResponseOk([
			'id' => $user->id,
			'phone' => $user->phone,
			'name' => $user->name,
			'nickname' => $user->nickname,
			'nickname_description' => $user->nickname_description,
			'roles' => $roles,
		]);
	}

	/**
	 * Display the specified resource.
	 */
	public function profileSave(Request $request)
	{
		//
	}

}
