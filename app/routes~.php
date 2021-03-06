<?php

use Cartalyst\Sentry\Users\UserNotFoundException;
use Cartalyst\Sentry\Groups\GroupNotFoundException;

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
	//return Redirect::route('overview.index');
	return Redirect::to(Session::get('loginRedirect', route('overviews.index')));
});

Route::group(array('before' => 'auth.sentry.root'), function()
{
	// Groups
	Route::bind('groups', function($id, $route) {
		# Disallow edit, update and delete root group
		// if ($route->getName() != 'groups.show' and ($id == 1 or  in_array($id, Sentry::getUser()->getGroups()->lists('id'))))
		// {
			// App::abort(403);
		// }
		
		return Sentry::getGroupProvider()->createModel()->findOrFail($id);
	});
	Route::resource('groups', 'GroupsController');
	
	// Users
	// Route::bind('users', function($id, $route) {
		# Disallow edit, update and delete root user
		// if ($route->getName() != 'users.show' and ($id == 1 or $id == Sentry::getUser()->id))
		// {
			// App::abort(403);
		// }
		
		// return Sentry::getUser()->findOrFail($id);
	// });
	// Route::resource('users', 'UsersController');
});

Route::group(array('before' => 'auth.sentry'), function()
{
    // Overview
	Route::resource('overviews', 'OverviewsController', array('only' => array('index')));
	
	// Account
	// Route::bind('accounts', function($id, $route) {
		// if (Sentry::getUser()->isSuperUser())
		// {
			// return Account::findOrFail($id);
		// }
		// else
		// {
			// return Sentry::getUser()->accounts()->findOrFail($id);
		// }
	// });
	// Route::resource('accounts', 'AccountsController');

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
