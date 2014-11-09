<?php

use Zizaco\Confide\ConfideUser;
use Zizaco\Confide\ConfideUserInterface;
use Zizaco\Entrust\HasRole;

class User extends Eloquent implements ConfideUserInterface {

    use ConfideUser;
	use HasRole;
	
	
	/**
	 * Many to many relationship.
	 *
	 * @return Model
	 */
	public function sites()
    {
		// Second argument is the name of pivot table.
		// Third & forth arguments are the names of foreign keys.
        return $this->belongsToMany('Site', 'site_user', 'user_id', 'site_id')->withTimestamps();
    }
}