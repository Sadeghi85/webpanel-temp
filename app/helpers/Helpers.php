<?php

class Helpers {

	private static $_exceptionErrorMessage = null;
	
	public static function getExceptionErrorMessage()
	{
		return self::$_exceptionErrorMessage;
	}
	
	public static function setExceptionErrorMessage($message)
	{
		self::$_exceptionErrorMessage = $message;
	}
	
	public static function activateTabIfRouteIs($routePattern, $class = 'active')
	{
		return (str_is($routePattern, Route::currentRouteName())) ? $class : '';
	}
	
}