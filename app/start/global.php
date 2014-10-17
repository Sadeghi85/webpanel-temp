<?php

/*
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/

ClassLoader::addDirectories(array(

	app_path().'/commands',
	app_path().'/controllers',
	app_path().'/models',
	app_path().'/database/seeds',

	app_path().'/helpers',
	app_path().'/libraries',
));

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a basic log file setup which creates a single file for logs.
|
*/

Log::useFiles(storage_path().'/logs/laravel.log');

/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
*/

App::fatal(function($exception)
{
	Log::error($exception);
	
	if ( ! Config::get('app.debug'))
	{
		$message = Helpers::getExceptionErrorMessage();

		return Request::ajax()
				? Response::json(array('error' => compact('message')), 500)
				: Response::view('errors.500', compact('message'), 500);
	}
});

App::error(function(Exception $exception, $code)
{
	Log::error($exception);
	
	$message = Helpers::getExceptionErrorMessage();
	
	if (Request::ajax() and $message and $code == 403)
		return Response::json(array('error' => compact('message')), 403);
	
	if ( ! Config::get('app.debug'))
	{
		switch ($code)
		{
			case 403:
				return Request::ajax()
						? Response::json(array('error' => compact('message')), 403)
						: Response::view('errors.403', compact('message'), 403);
				
			case 405:
				return Request::ajax()
						? Response::json(array('error' => compact('message')), 405)
						: Response::view('errors.405', compact('message'), 405);

			case 500:
				return Request::ajax()
						? Response::json(array('error' => compact('message')), 500)
						: Response::view('errors.500', compact('message'), 500);
				
			case 503:
				return Request::ajax()
						? Response::json(array('error' => compact('message')), 503)
						: Response::view('errors.503', compact('message'), 503);

			default:
				return Request::ajax()
						? Response::json(array('error' => compact('message')), 404)
						: Response::view('errors.404', compact('message'), 404);
		}
	}
});

App::error(function(Illuminate\Session\TokenMismatchException $exception, $code)
{
	$message = Helpers::getExceptionErrorMessage();
	
    return Request::ajax()
			? Response::json(array('error' => compact('message')), 403)
			: Response::view('errors.403', compact('message'), 403);
});

App::error(function(Illuminate\Database\Eloquent\ModelNotFoundException $exception, $code)
{
	$message = Helpers::getExceptionErrorMessage();
    
	return Request::ajax()
			? Response::json(array('error' => compact('message')), 404)
			: Response::view('errors.404', compact('message'), 404);
});

App::missing(function($exception)
{
    $message = Helpers::getExceptionErrorMessage();
    
	return Request::ajax()
			? Response::json(array('error' => compact('message')), 404)
			: Response::view('errors.404', compact('message'), 404);
});

/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenance mode is in effect for the application.
|
*/

App::down(function()
{
	$message = 'Be right back!';
	
	return Request::ajax()
			? Response::json(array('error' => compact('message')), 503)
			: Response::view('errors.503', compact('message'), 503);
});

/*
|--------------------------------------------------------------------------
| Require The Filters File
|--------------------------------------------------------------------------
|
| Next we will load the filters file for the application. This gives us
| a nice separate location to store our route and application filter
| definitions instead of putting them all in the main routes file.
|
*/

require app_path().'/filters.php';

// Paginator page must be positive
Input::merge(array(Paginator::getPageName() => abs(Input::get(Paginator::getPageName(), 1))));

/*
|--------------------------------------------------------------------------
| Validator Extends
|--------------------------------------------------------------------------
|
*/

Validator::extend('custom.domain', function($attribute, $value, $parameters)
{
	$inputs = explode("\r\n", trim($value));

	foreach ($inputs as $input)
	{
		if (preg_match('#^\d+(?:\.\d+)*$#', $input))
		{
			return false;
		}

		if ( ! preg_match('#^(?=.{1,255}$)[0-9A-Za-z](?:(?:[0-9A-Za-z]|\b-){0,61}[0-9A-Za-z])?(?:\.[0-9A-Za-z](?:(?:[0-9A-Za-z]|\b-){0,61}[0-9A-Za-z])?)*$#', $input))
		{
			return false;
		}
	}

	return true;
});

Validator::extend('custom.domain', function($attribute, $value, $parameters)
{
	$inputs = explode("\r\n", trim($value));
	
	foreach ($inputs as $input)
	{
		if (preg_match('#^\d+(?:\.\d+)*$#', $input))
		{
			return false;
		}
		
		if ( ! preg_match('#^(?=.{1,255}$)[0-9A-Za-z](?:(?:[0-9A-Za-z]|\b-){0,61}[0-9A-Za-z])?(?:\.[0-9A-Za-z](?:(?:[0-9A-Za-z]|\b-){0,61}[0-9A-Za-z])?)*$#', $input))
		{
			return false;
		}
	}
	
	return true;
});

Validator::extend('custom.exists_array', function($attribute, $value, $parameters)
{
	if (count($parameters) != 2) { return false; }
	
	if ( ! is_array($value))
	{
		$inputs = array($value);
	}
	else
	{
		$inputs = $value;
	}
	
	foreach ($inputs as $input)
	{
		$validator = Validator::make(array($attribute => $input), array($attribute => sprintf('exists:%s,%s', $parameters[0], $parameters[1])));

		if ($validator->fails()) { return false; }
	}
	
	return true;
});

/*
|--------------------------------------------------------------------------
| Blade Extends
|--------------------------------------------------------------------------
|
*/

Blade::extend(function($value)
{
	return preg_replace('/@php((.|\s)*?)@endphp/', '<?php $1 ?>', $value);
});

Blade::extend(function($value)
{
	return preg_replace_callback('/@comment((.|\s)*?)@endcomment/',
              function ($matches) {
                    return '<?php /* ' . preg_replace('/@|\{/', '\\\\$0\\\\', $matches[1]) . ' */ ?>';
              },
              $value
			);
});

/*
|--------------------------
| Events
|--------------------------
*/


/*
|--------------------------
| View Composers
|--------------------------
*/

View::composer(Paginator::getViewName(), function($view)
{
	$queryString = array_except(Input::query(), array(Paginator::getPageName()));
	$view->paginator->appends($queryString);
});

/*
|--------------------------------------------------------------------------
| Global Constant
|--------------------------------------------------------------------------
|
*/

