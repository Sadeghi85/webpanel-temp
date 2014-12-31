<?php

class SitesController extends BaseController {
	
	public function index()
	{
		if (Request::ajax()) {
			return Response::json(Site::getIndexData());
		}
		
		$page = Input::get('page', 1);
		$pageSize = Input::get('pageSize', 10);
		
		return View::make('sites.index', compact('page', 'pageSize'));
	}

	public function store()
	{
		if ( ! Confide::user()->ability('Administrator', 'create_site')) {
			Helpers::setExceptionErrorMessage('You don\'t have permission to access this resource.');
			App::abort(403);
		}
		
		if ( ! Site::addSite()) {
			Helpers::setExceptionErrorMessage(Site::getValidationMessage());
			App::abort(403);
		}
		
		return Response::json(array());
	}

	public function getChangeState($site)
	{
		Site::changeState($site);
		
		return Redirect::route('sites.index', array('page' => Input::get('page', 1), 'pageSize' => Input::get('pageSize', 10)));
	}
	
	public function getDetails($site)
	{
		$id = $site->id;
		$tag = $site->tag;
		
		return View::make('sites.details', compact('id', 'tag'));
	}
	
	public function getDetailsSettingsMain($site)
	{
		$id = $site->id;
		$port = $site->aliases()->where('server_name', '=', 1)->pluck('port');
		$serverName = $site->aliases()->where('server_name', '=', 1)->pluck('alias');
		
		$aliases = implode("\r\n", $site->aliases()->where('server_name', '=', 0)->lists('alias'));
		
		return View::make('sites.details-settings-main', compact('id', 'port', 'serverName', 'aliases'));
	}
	
	// update main settings: aliases
	public function postDetailsSettingsMainAliases($site)
	{	
		if ( ! Confide::user()->ability('Administrator', 'edit_site')) {
			Helpers::setExceptionErrorMessage('You don\'t have permission to access this resource.');
			App::abort(403);
		}

		if ( ! Site::updateAliases($site)) {
			Helpers::setExceptionErrorMessage(Site::getValidationMessage());
			App::abort(403);
		}
		
		return Response::json(array());
	}
	
	// update main settings: server_name
	public function postDetailsSettingsMainServerName($site)
	{	
		if ( ! Confide::user()->ability('Administrator', 'edit_site')) {
			Helpers::setExceptionErrorMessage('You don\'t have permission to access this resource.');
			App::abort(403);
		}

		if ( ! Site::updateServerName($site)) {
			Helpers::setExceptionErrorMessage(Site::getValidationMessage());
			App::abort(403);
		}
		
		return Response::json(array());
	}
	
	// update main settings: port
	public function postDetailsSettingsMainPort($site)
	{	
		if ( ! Confide::user()->ability('Administrator', 'edit_site')) {
			Helpers::setExceptionErrorMessage('You don\'t have permission to access this resource.');
			App::abort(403);
		}

		if ( ! Site::updatePort($site)) {
			Helpers::setExceptionErrorMessage(Site::getValidationMessage());
			App::abort(403);
		}
		
		return Response::json(array());
	}

	public function destroy($site)
	{
		if ( ! Confide::user()->ability('Administrator', 'remove_site')) {
			Helpers::setExceptionErrorMessage('You don\'t have permission to access this resource.');
			App::abort(403);
		}
		
		if ( ! Site::removeSite($site)) {
			Helpers::setExceptionErrorMessage('Unable to remove this site.');
			App::abort(403);
		}
		
		return Response::json(array());
	}
}