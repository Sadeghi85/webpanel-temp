<?php

class Site extends \Eloquent {
	protected $fillable = [];
	
	
	/**
     * Many to many relationship.
     *
     * @return Model
     */
    public function users()
    {
        // Second argument is the name of pivot table.
        // Third & forth arguments are the names of foreign keys.
        return $this->belongsToMany('User', 'site_user', 'site_id', 'user_id')->withTimestamps();
        
    }
	
	/**
     * One to many relationship.
     *
     * @return Model
     */
	public function aliases()
    {
		// Second & third arguments are the names of foreign key and local key.
        return $this->hasMany('Alias', 'site_id', 'id');
    }
	
}