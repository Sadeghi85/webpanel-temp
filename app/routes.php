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
	Route::get('overviews', array('as' => 'overviews.index', function () {
		return View::make('overviews.index');
	}));
	Route::get('overviews/disk', array('as' => 'overviews.disk', function () {
		return View::make('overviews.disk');
	}));
	Route::get('overviews/memory', array('as' => 'overviews.memory', function () {
		return View::make('overviews.memory');
	}));
	


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
		
		return $resourceUser;
	});
	Route::resource('users', 'UsersController', array('only' => array('index', 'store', 'update', 'destroy')));
	Route::get('users/sites/{id}', array('as' => 'users.sites', 'uses' => 'UsersController@sites'));
	
	Route::resource('roles', 'RolesController', array('only' => array('index')));
	
	// Sites
	Route::model('site', 'Site', function() {
		Helpers::setExceptionErrorMessage('This site doesn\'t exist.');
		App::abort(403);
	});
	Route::get('sites', array('as' => 'sites.index', 'uses' => 'SitesController@index'));
	Route::post('sites/store', array('as' => 'sites.store', 'uses' => 'SitesController@store'));
	Route::post('sites/destroy/{site}', array('as' => 'sites.destroy', 'uses' => 'SitesController@destroy'));
	Route::get('sites/details/{site}', array('as' => 'sites.get-details', 'uses' => 'SitesController@getDetails'));
	// update main settings
	Route::get('sites/details-settings-main/{site}', array('as' => 'sites.get-details-settings-main', 'uses' => 'SitesController@getDetailsSettingsMain'));
	Route::post('sites/details-settings-main/{site}', array('as' => 'sites.post-details-settings-main', 'uses' => 'SitesController@postDetailsSettingsMain'));
	
	// Route::post('sites/details-settings-main-port/{site}', array('as' => 'sites.post-details-settings-main-port', 'uses' => 'SitesController@postDetailsSettingsMainPort'));
	// Route::post('sites/details-settings-main-servername/{site}', array('as' => 'sites.post-details-settings-main-servername', 'uses' => 'SitesController@postDetailsSettingsMainServerName'));
	// Route::post('sites/details-settings-main-aliases/{site}', array('as' => 'sites.post-details-settings-main-aliases', 'uses' => 'SitesController@postDetailsSettingsMainAliases'));
	
	// change status
	Route::get('sites/change-state/{site}', array('as' => 'sites.change-state', 'uses' => 'SitesController@getChangeState'));
	
	
	

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
