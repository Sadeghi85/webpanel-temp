<?php

class SitesController extends BaseController {

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
			//Input::merge(array('sort' => Input::get('sort', array(array('field' => 'tag', 'dir' => 'asc')))));
			
			list($_sites, $sitesCount) = Helpers::getGridData(
										Site::with('aliases')->join('site_aliases', 'site_aliases.site_id', '=', 'sites.id')
										->select(array('sites.id as id', 'sites.activated as activated', 'sites.tag as tag', 'site_aliases.alias as alias'))
										);

			$sites = array();
			foreach ($_sites->toArray() as $site) {
				$aliases = '';
				foreach ($site['aliases'] as $alias) {
					$aliases .= $alias['alias'].':'.$alias['port'].', ';
				}
				
				$sites[] = array(
					'id' => $site['id'],
					'activated' => $site['activated'],
					'tag' => $site['tag'],
					'alias' => trim(trim($aliases), ','),
				);
			}

			return Response::json(array('data' => $sites, 'total' => $sitesCount));
		}
		
		return View::make('sites.index');
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