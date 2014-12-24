<?php

class SitesController extends BaseController {

	public function getDetails($site)
	{
		$tag = $site->tag;
		
		return View::make('sites.details', compact('id', 'tag'));
	}
	
	public function getDetailsSettingsAliases($site)
	{
		$serverName = $site->aliases()->where('server_name', '=', 1)->first();
		$serverName = sprintf('%s:%s', $serverName['alias'], $serverName['port']);
		
		$aliases = '';
		$_aliases = $site->aliases()->where('server_name', '=', 0)->get();
		foreach ($_aliases as $_alias) {
			$aliases .= sprintf('%s:%s%s', $_alias['alias'], $_alias['port'], "\r\n");
		}
		
		return View::make('sites.details-settings-aliases', compact('id', 'serverName', 'aliases'));
	}
	
	// update aliases
	public function postDetailsSettingsAliases($site)
	{
		if ( ! Confide::user()->ability('Administrator', 'edit_site')) {
			Helpers::setExceptionErrorMessage('You don\'t have permission to access this resource.');
			App::abort(403);
		}
		
		if ($site->updateValidationFails()) {
			Helpers::setExceptionErrorMessage($site->getValidationMessage());
			App::abort(403);
		}
		
		if ( ! Site::updateSite($site)) {
			Helpers::setExceptionErrorMessage('Unable to update this site.');
			App::abort(403);
		}
		
		return Response::json(array());
	}
	
	public function getChangeState($site)
	{
		Site::changeState($site);
		
		return Redirect::route('sites.index', array('page' => Input::get('page', 1), 'pageSize' => Input::get('pageSize', 10)));
	}
	
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