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
		$resourceUser  = User::findOrFail($id);
		$administrator = User::where('username', '=', 'administrator')->first();
		$loggedinUser  = Confide::user();
		
		# only users with "Administrator" role or "manage_user" permission can enter here
		if ( ! $loggedinUser->ability('Administrator', 'manage_user')) {
			App::abort(403);
		}
		
		# disallow removing "administrator" user or logged-in user
		if ($route->getName() == 'users.destroy' and ($resourceUser->id == $administrator->id or $resourceUser->id == $loggedinUser->id)) {
			App::abort(403);
		}
		
		if ($loggedinUser->id != $administrator->id) {
			# users with "Administrator" role or "manage_user" permission that aren't "administrator" user can't manage users of same type
			if ($loggedinUser->id != $resourceUser->id and $resourceUser->ability('Administrator', 'manage_user')) {
				App::abort(403);
			}
			
			# only "administrator" user can edit itself
			if ($resourceUser->id == $administrator->id) {
				App::abort(403);
			}
		}

		return $user;
	});
	Route::resource('users', 'UsersController', array('only' => array('index', 'update', 'destroy'));

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
