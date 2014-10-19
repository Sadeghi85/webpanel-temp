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
	
	// Roles
	Route::bind('roles', function($id, $route) {
		$role = Role::findOrFail($id);
	
		// # Only Administrator role can edit or remove roles
		if (($route->getName() == 'roles.destroy' or $route->getName() == 'roles.update') and ! Entrust::hasRole('Administrator')) {
			App::abort(403);
		}
		
		// # Disallow removing Administrator role
		if ($route->getName() == 'roles.destroy' and $role->pluck('name') == 'Administrator') {
			App::abort(403);
		}
		
		return $role;
	});
	Route::resource('roles', 'RolesController');
	
	// Users
	Route::bind('users', function($id, $route) {
		$user = User::findOrFail($id);
		$username = $user->pluck('username');
		$loggedinUser = Auth::user();
		$admin = User::where('username', '=', 'administrator')->first();
		
		if ( ! $loggedinUser->ability('Administrator', 'manage_user')) {
			App::abort(403);
		}
		
		// # Only administrator user can edit itself
		if ($route->getName() == 'users.update' and $id == $admin->id and $loggedinUser->id != $admin->id) {
			App::abort(403);
		}
		
		// # Disallow removing administrator user or logged-in user
		if ($route->getName() == 'users.destroy' and ($username == 'administrator' or $id == $loggedinUser->id)) {
			App::abort(403);
		}
		
		if ($loggedinUser->id != $admin->id and $user->can('manage_user')) {
			App::abort(403);
		}
		
		
		return $user;
	});
	Route::resource('users', 'UsersController');

	// Log
	//Route::resource('logs', 'PanelLogsController', array('only' => array('index', 'show', 'destroy')));

	// Profile
	//Route::resource('profile', 'ProfileController', array('only' => array('index')));
	
});

/*
|--------------------------------------------------------------------------
| Authentication and Authorization Routes
|--------------------------------------------------------------------------
*/
Route::group(array('prefix' => 'auth'), function()
{
	// Login
	Route::get('login', array('as' => 'auth.login', 'uses' => 'AuthController@getLogin'));
	Route::post('login', array('uses' => 'AuthController@postLogin'));
	
	// Logout
	Route::get('logout', array('as' => 'auth.logout', 'uses' => 'AuthController@getLogout'));
});
//

// Confide routes
Route::get('users/create', 'UsersController@create');
Route::post('users', 'UsersController@store');
Route::get('users/login', 'UsersController@login');
Route::post('users/login', 'UsersController@doLogin');
Route::get('users/confirm/{code}', 'UsersController@confirm');
Route::get('users/forgot_password', 'UsersController@forgotPassword');
Route::post('users/forgot_password', 'UsersController@doForgotPassword');
Route::get('users/reset_password/{token}', 'UsersController@resetPassword');
Route::post('users/reset_password', 'UsersController@doResetPassword');
Route::get('users/logout', 'UsersController@logout');
