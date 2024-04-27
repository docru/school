<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index()
	{
		return $this->ResponseOk($this->_groupList()->toArray());
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request)
	{
		if (empty($request->course_id)) {
			return $this->ResponseError('Не указана группа');
		}
		if (empty($request->name)) {
			return $this->ResponseError('Не указан название группы');
		}

		$group = new Group(['name' => $request->name, 'course_id' => $request->course_id]);
		$group->save();
		if (empty($group->id)) {
			return $this->ResponseError('Ошибка при создании группы');
		}

		return $this->ResponseOk($this->_groupList()->toArray());
	}

	/**
	 * Display the specified resource.
	 */
	public function show(Group $group)
	{
		return $this->ResponseOk($group->toArray());
	}
	/**
	 * Display the specified resource.
	 */
	public function getById($id)
	{
		$result = Group::whereId($id)->first();
		if($result){
			return $this->ResponseOk([$result]);
		}else {
			return $this->ResponseError('Что-то пошло не так');
		}

	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, Group $group)
	{
		$group->update($request->all());
		return $this->ResponseOk($group->toArray());
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Group $group)
	{
		$group->delete();
		return $this->ResponseOk($this->_groupList());
	}

	private function _groupList()
	{
		return Group::all();
	}
}
