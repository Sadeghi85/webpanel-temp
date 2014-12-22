<?php

class SitesController extends BaseController {

	public function getDetails($id)
	{
		$site = Site::findOrFail($id);
		$tag = $site->tag;
		
		return View::make('sites.details', compact('id', 'tag'));
	}
	
	public function getDetailsSettingsAliases($id)
	{
		$site = Site::findOrFail($id);
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
	public function putDetailsSettingsAliases($id)
	{
		$site = Site::findOrFail($id);
		
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
	
	public function index()
	{
		if (Request::ajax()) {
			return Response::json(Site::getIndexData());
		}
		
		return View::make('sites.index');
	}

	public function store()
	{
		$site = new Site;
		
		if ($site->validationFails()) {
			Helpers::setExceptionErrorMessage($site->getValidationMessage());
			App::abort(403);
		}
		
		if ( ! Site::addSite()) {
			Helpers::setExceptionErrorMessage('Unable to create this site.');
			App::abort(403);
		}
		
		return Response::json(array());
	}

	public function destroy($site)
	{
		if ( ! Site::removeSite($site)) {
			Helpers::setExceptionErrorMessage('Unable to remove this site.');
			App::abort(403);
		}
		
		return Response::json(array());
	}
}