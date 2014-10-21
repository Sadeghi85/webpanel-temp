<?php

class RolesController extends BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /overviews
	 *
	 * @return Response
	 */
	public function index()
	{
		if (Request::ajax())
		{
			Input::merge(array('sort' => Input::get('sort', array(array('field' => 'id', 'dir' => 'asc')))));
			
			list($_roles, $rolesCount) = Helpers::getGridData(Role::with('perms')->select('id', 'name'));
			
			$roles = array();
			foreach ($_roles->toArray() as $role) {
				$perms = '';
				foreach ($role['perms'] as $perm) {
					$perms .= $perm['name'].', ';
				}
				
				$roles[] = array(
					'id' => $role['id'],
					'name' => $role['name'],
					'permissions' => trim(trim($perms), ','),
				);
			}

			return Response::json(array('data' => $roles, 'total' => $rolesCount));
		}
		
		return View::make('roles.index');
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /overviews/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /overviews
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /overviews/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /overviews/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /overviews/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /overviews/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}