<?php

class Alias extends \Eloquent {
	protected $fillable = array('alias', 'port', 'server_name');
	protected $table = 'site_aliases';
	
	
	public function site()
	{
		return $this->belongsTo('Site');
	}
	
}