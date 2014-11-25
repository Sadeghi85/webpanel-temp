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
			return Response::json(Site::getIndexData());
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
		$siteInstance = new Site;
		
		if ($siteInstance->validationFails()) {
			Helpers::setExceptionErrorMessage($siteInstance->getValidationMessage());
			App::abort(403);
		}
		
		if ( ! Site::addSite()) {
			Helpers::setExceptionErrorMessage('Unable to create this site.');
			App::abort(403);
		}
		
		return Response::json(array());
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
	public function destroy($site)
	{
		if ( ! Site::removeSite($site)) {
			Helpers::setExceptionErrorMessage('Unable to remove this site.');
			App::abort(403);
		}
		
		return Response::json(array());
	}

}