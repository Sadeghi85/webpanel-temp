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
	
	
	public static function getIndexData() {
		//Input::merge(array('sort' => Input::get('sort', array(array('field' => 'tag', 'dir' => 'asc')))));
		
		list($_sites, $sitesCount) = Helpers::getGridData(
									Site::with('aliases')->join('site_aliases', 'site_aliases.site_id', '=', 'sites.id')
									->select(array('sites.id as id', 'sites.activated as activated', 'sites.tag as tag', 'site_aliases.alias as alias'))
									);

		$sites = array();
		foreach ($_sites->toArray() as $site) {
			$aliases = array(0 => 'dummy');
			foreach ($site['aliases'] as $alias) {
				if ($alias['server_name']) {
					$aliases[0] = '*'.$alias['alias'].':'.$alias['port'];
				} else {
					$aliases[] = $alias['alias'].':'.$alias['port'];
				}
			}
			
			$sites[] = array(
				'id' => $site['id'],
				'activated' => $site['activated'],
				'tag' => $site['tag'],
				'alias' => implode(', ', $aliases),
			);
		}

		return array('data' => $sites, 'total' => $sitesCount);
	}
	
	
}