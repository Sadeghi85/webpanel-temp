<?php

class SiteSettings extends \Eloquent {
	protected $fillable = [];
	protected $table = 'site_settings';
	
	
	public function site()
	{
		return $this->belongsTo('Site')->withTimestamps();
	}
	
}