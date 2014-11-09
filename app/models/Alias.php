<?php

class Alias extends \Eloquent {
	protected $fillable = [];
	protected $table = 'site_aliases';
	
	
	public function site()
	{
		return $this->belongsTo('Site');
	}
	
}