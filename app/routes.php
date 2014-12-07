<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return Redirect::route('overviews.index');
});

Route::group(array('before' => 'auth'), function()
{
    // Overview
	Route::resource('overviews', 'OverviewsController', array('only' => array('index')));

	// Users
	Route::bind('users', function($id, $route) {
		$resourceUser  = User::find($id);
		if ( ! $resourceUser) {
			Helpers::setExceptionErrorMessage('This user doesn\'t exist.');
			App::abort(403);
		}
		$administrator = User::where('username', '=', 'administrator')->first();
		$loggedinUser  = Confide::user();
		
		# only users with "Administrator" role or "manage_user" permission can enter here
		if ( ! $loggedinUser->ability('Administrator', 'manage_user')) {
			Helpers::setExceptionErrorMessage('You don\'t have permission to access this resource.');
			App::abort(403);
		}
		
		# disallow removing "administrator" user or logged-in user
		if ($route->getName() == 'users.destroy' and ($resourceUser->id == $administrator->id or $resourceUser->id == $loggedinUser->id)) {
			Helpers::setExceptionErrorMessage('You don\'t have permission to access this resource.');
			App::abort(403);
		}
		
		if ($loggedinUser->id != $administrator->id) {
			# users with "Administrator" role or "manage_user" permission that aren't "administrator" user can't manage users of same type
			if ($loggedinUser->id != $resourceUser->id and $resourceUser->ability('Administrator', 'manage_user')) {
				Helpers::setExceptionErrorMessage('You don\'t have permission to access this resource.');
				App::abort(403);
			}
			
			# only "administrator" user can edit itself
			if ($resourceUser->id == $administrator->id) {
				Helpers::setExceptionErrorMessage('You don\'t have permission to access this resource.');
				App::abort(403);
			}
		}
		
		if ($route->getName() == 'users.store') {
			return;
		}
		
		return $user;
	});
	Route::resource('users', 'UsersController', array('only' => array('index', 'store', 'update', 'destroy')));
	Route::get('users/sites/{id}', array('as' => 'users.sites', 'uses' => 'UsersController@sites'));
	
	Route::resource('roles', 'RolesController', array('only' => array('index')));
	
	// Sites
	Route::bind('sites', function($id, $route) {
		if ($route->getName() == 'sites.destroy' and ! Confide::user()->ability('Administrator', 'remove_site')) {
			Helpers::setExceptionErrorMessage('You don\'t have permission to access this resource.');
			App::abort(403);
		}
		
		if ($route->getName() == 'sites.update' and ! Confide::user()->ability('Administrator', 'edit_site')) {
			Helpers::setExceptionErrorMessage('You don\'t have permission to access this resource.');
			App::abort(403);
		}
		
		if ($route->getName() == 'sites.store') {
			if ( ! Confide::user()->ability('Administrator', 'create_site')) {
				Helpers::setExceptionErrorMessage('You don\'t have permission to access this resource.');
				App::abort(403);
			}
			
			return;
		}

		$site = Site::find($id);
		
		if ( ! $site) {
			Helpers::setExceptionErrorMessage('This site doesn\'t exist.');
			App::abort(403);
		}
		
		return $site;
	});
	Route::resource('sites', 'SitesController', array('only' => array('index', 'store', 'update', 'destroy')));

	// Log
	//Route::resource('logs', 'PanelLogsController', array('only' => array('index', 'show', 'destroy')));
	
});

/*
|--------------------------------------------------------------------------
| Authentication and Authorization Routes
|--------------------------------------------------------------------------
*/
Route::group(array('prefix' => 'auth'), function()
{
	// Login
	Route::get('login', array('as' => 'auth.login', 'uses' => 'AuthController@login'));
	Route::post('login', array('uses' => 'AuthController@doLogin'));
	
	// Logout
	Route::get('logout', array('as' => 'auth.logout', 'uses' => 'AuthController@logout'));
});
