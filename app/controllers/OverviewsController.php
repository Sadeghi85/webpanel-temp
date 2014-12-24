<?php

class OverviewsController extends BaseController {

	public function index()
	{
		return View::make('overviews.index');
	}
	
	public function getDiskInfo()
	{
		return View::make('overviews.disk');
	}

	
}