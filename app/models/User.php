<?php

use Zizaco\Confide\ConfideUser;
use Zizaco\Confide\ConfideUserInterface;
use Zizaco\Entrust\HasRole;

class User extends Eloquent implements ConfideUserInterface {

    use ConfideUser;
	use HasRole;
	
	//protected $fillable = [];
	protected $guarded = array('id');
	protected $table = 'users';
	
	private $validationRules = array(
		'username'   => 'required|alpha_dash|between:3,127|unique:users,username',
		'email'      => 'required|unique:users,email',
		'password'   => 'required|between:4,64|confirmed',
		'password_confirmation'   => 'required|between:4,64',
		'role' => 'required|exists:roles,id'
	);
	
	private $validator;
	private $validationMessage;
	
	public function validationPasses() {
		$username = strtolower(Input::get('username'));
	
		Input::merge(array(
			'username' => $username,
			'email'   => $username.'@localhost.localdomain',
		));
		
		$v = Validator::make(Input::all(), $this->validationRules, array());
		$v->setAttributeNames(array(
							'username' => 'Username',
							'password'  => 'Password',
							'password_confirmation'  => 'Password Confirmation',
							'role'  => 'Role',
							)
		);
		
		if ($v->fails()) {
			
			$this->validator = $v;
			$this->validationMessage = $v->messages()->first();
			
			return false;
		}
		
		return true;
	}
	
	public function validationFails() {
		return ( ! $this->validationPasses());
	}
	
	public function getValidator() {
		return $this->validator;
	}
	
	public function getValidationMessage() {
		return $this->validationMessage;
	}
	
	public function setValidationRules(array $newRules)	{
		$this->validationRules = array_replace($this->validationRules, $newRules);
	}
	
	public function updateValidationRules(array $newRules)	{
		$this->validationRules = array_merge($this->validationRules, $newRules);
	}
	
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
	
	public static function addUser() {
		$user = new User;
		$user->username = input::get('username');
		$user->name = input::get('name');
		$user->email = input::get('email');
		$user->password = input::get('password');
		$user->password_confirmation = input::get('password_confirmation');
		$user->activated = 1;
			
		if (OS::addUser(input::get('username'), Config::get('panel.web_base_dir'), 'apache', 'WebPanel user', '/sbin/nologin', 0, input::get('password'))) {
			$roleId = input::get('role');
			//$role = Role::find($roleId);

			$user->save();
			Debugbar::info($user);
			$user->roles()->sync(array($roleId));
			//$user->attachRole($role);

		} else {
			return false;
		}
		
		return true;
	}
	
	public static function getSiteData($userId) {
		$_users = User::where('id', '=', $userId)->with('sites.aliases')->get();
		
		$sites = array();
		$sitesCount = 0;
		
		foreach ($_users->toArray() as $_user) {
			
			$sitesCount = count($_user['sites']);
			
			foreach ($_user['sites'] as $site) {
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
					'tag' => $site['tag'],
					'aliases' => implode(', ', $aliases),
				
				);
			}
		}
		
		return array('data' => $sites, 'total' => $sitesCount);
	}
	
	public static function removeUser($user) {
		if (OS::removeUser($user->username)) {
			
			$user->delete();
		
			return true;
		} else {
			return false;
		}
	}
	
	public static function changeState($user)
	{
		$username = $user->username;

		if ($user->activated) {
			if (OS::disableUser($username)) {
				$user->activated = 0;
				$user->save();
			} else { return false; }
		} else {
			if (OS::enableUser($username)) {
				$user->activated = 1;
				$user->save();
			} else { return false; }
		}
		
		return true;
	}
	
	public static function getIndexData() {
		//// Input::merge(array('sort' => Input::get('sort', array(array('field' => 'tag', 'dir' => 'asc')))));
		
		@list($_users, $usersCount) = Helpers::getGridData(
			User::with('roles')
			->join('assigned_roles', 'users.id', '=', 'assigned_roles.user_id')
			->join('roles', 'roles.id', '=', 'assigned_roles.role_id')
			->select(array('users.id as id', 'users.username as username', 'users.activated as activated', 'users.name as name', 'roles.name as role'))
		);
		
		// $sql= <<<EOT
// (
	// SELECT id, CONCAT(tag,' ',aliases) AS sites FROM
	// (
		// SELECT id, tag, GROUP_CONCAT(alias SEPARATOR ' ') AS aliases FROM
		// (
			// SELECT sites.id AS id, sites.tag AS tag, site_aliases.alias AS alias FROM sites
			// JOIN site_aliases
			// ON sites.id = site_aliases.site_id
		// ) AS sites
		// GROUP BY tag
	// ) AS sites
// ) AS sites

// EOT;
		// @list($_users, $usersCount) = Helpers::getGridData(
				// User::with('sites.aliases')
				// ->join('site_user', 'users.id', '=', 'site_user.user_id')
				// ->join(DB::raw($sql), function($join)
				// {
					// $join->on('site_user.site_id', '=', 'sites.id');
				// })
				
				// ->select(array('users.id as id', 'users.username as username', 'users.name as name', 'sites.sites as sites'))
			// );
			
//Debugbar::info($_users->toArray());
		$users = array();
		
		foreach ($_users->toArray() as $_user) {
			$_roles = array();
			
			foreach ($_user['roles'] as $_role) {
				
				
				$_roles[] = $_role['name'];

				
				//$obj = new StdClass;
				//$obj->name = sprintf('%s ( %s )', $_site['tag'], implode(', ', $aliases));
				//$sites[] = $obj;
			}
			
			
			
			$users[] = array(
				'id' => $_user['id'],
				'username' => sprintf('<a href="%s">%s</a>', route('users.get-details', array('user' => $_user['id'])), $_user['username']),
				'activated' => sprintf('<a href="%s" class="activated">%s</a>', route('users.change-state', array('user' => $_user['id'])), $_user['activated'] ? 'Yes' : 'No'),
				'name' => $_user['name'],
				'role' => implode(', ', $_roles),
				
				//'activated' => $_user['activated'],
				
				//'sites' => sprintf('%s%s%s', '<li>', Form::select('sites', $sites, null, array('class' => 'sites')), '</li>'),
			);
		}

		$usersCount = User::count();
		return array('data' => $users, 'total' => $usersCount);
	}
}