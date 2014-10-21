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
	
	private static function _getGridFilter($operator, $value)
	{
		switch ($operator)
		{
			case 'eq':
				$operator = '=';
				break;
			case 'neq':
				$operator = '<>';
				break;
			case 'lt':
				$operator = '<';
				break;
			case 'lte':
				$operator = '<=';
				break;
			case 'gt':
				$operator = '>';
				break;
			case 'gte':
				$operator = '>=';
				break;
			case 'startswith':
				$operator = 'LIKE';
				$value = str_replace('_', '\\_', $value.'%');
				break;
			case 'endswith':
				$operator = 'LIKE';
				$value = str_replace('_', '\\_', '%'.$value);
				break;
			case 'contains':
				$operator = 'LIKE';
				$value = str_replace('_', '\\_', '%'.$value.'%');
				break;
			case 'doesnotcontain':
				$operator = 'NOT LIKE';
				$value = str_replace('_', '\\_', '%'.$value.'%');
				break;
		}

		return array($operator, $value);
	}
	
	public static function getGridData($modelObject)
	{
		try {
			$filter = Input::get('filter', array(array(array())));
			$sort = Input::get('sort', array(array()));
			$skip = Input::get('skip', 0);
			$take = Input::get('take', 10);

			if (isset($filter['filters'][0]['field'], $filter['filters'][0]['operator'], $filter['filters'][0]['value'])) {
				$field = $filter['filters'][0]['field'];
				$operator = $filter['filters'][0]['operator'];
				$value = $filter['filters'][0]['value'];
				
				list($operator, $value) = self::_getGridFilter($operator, $value);
				
				$modelObject = $modelObject->where($field, $operator, $value);			
			}
			
			if (isset($filter['filters'][1]['field'], $filter['filters'][1]['operator'], $filter['filters'][1]['value'])) {
				$field = $filter['filters'][1]['field'];
				$operator = $filter['filters'][1]['operator'];
				$value = $filter['filters'][1]['value'];
				
				list($operator, $value) = self::_getGridFilter($operator, $value);
				
				if (isset($filter['logic']) and ($filter['logic'] == 'or')) {
					$modelObject = $modelObject->orWhere($field, $operator, $value);
				}
				else {
					$modelObject = $modelObject->where($field, $operator, $value);
				}
			}
			
			if (isset($sort[0]['field'], $sort[0]['dir'])) {
				$field = $sort[0]['field'];
				$dir = $sort[0]['dir'];
				
				$modelObject = $modelObject->orderBy($field, $dir);
			}
			
			$total = $modelObject->count();
			
			$modelObject = $modelObject->skip($skip)->take($take);
			
			return array($modelObject->get(), $total);
		} catch (Exception $e) {
			App::abort(403);
		}
	}
	
}