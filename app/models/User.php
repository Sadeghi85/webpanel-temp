<?php

use Zizaco\Confide\ConfideUser;
use Zizaco\Confide\ConfideUserInterface;
use Zizaco\Entrust\HasRole;

class User extends Eloquent implements ConfideUserInterface {

    use ConfideUser;
	use HasRole;
	
	protected $fillable = [];
	protected $table = 'users';
	
	private $validationRules = array(
		'username'   => 'required|between:3,127|unique:users,username',
		'email'      => 'required|unique:users,email',
		'password'   => 'required|between:3,127|confirmed',
		'password_confirmation'   => 'required|between:3,127',
	);
	
	private $validator;
	private $validationMessage;
	
	public function validationPasses() {
		$username = Input::get('username');
	
		Input::merge(array(
			'email'   => $username.'@localhost.localdomain',
		));
		
		$v = Validator::make(Input::all(),
							$this->validationRules,
							array(
									//'custom.domain' => ':attribute is not valid.',
									//'unique_with' => 'This combination of :fields already exists.',
							)
						);
		$v->setAttributeNames(array(
							'username' => '"Username"',
							'password'  => '"Password"',
							)
		);
		
		if ($v->fails()) {
			
			$this->validator = $v;
			
			// $fail = $v->failed();
			// if (isset($fail['alias']['Unique_with'])) {
				// $this->validationMessage = sprintf('"%s" is already taken.', Input::get('server-name'));
			// } else {
				$this->validationMessage = $v->messages()->first();
			//}
			
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
	
	
	public static function removeUser($user) {

	}
	
	public static function addUser($user) {
		
	}
	
	public static function getIndexData() {
		//// Input::merge(array('sort' => Input::get('sort', array(array('field' => 'tag', 'dir' => 'asc')))));
		
		$sql= <<<EOT
(
	SELECT id, CONCAT(tag,' ',aliases) AS sites FROM
	(
		SELECT id, tag, GROUP_CONCAT(alias SEPARATOR ' ') AS aliases FROM
		(
			SELECT sites.id AS id, sites.tag AS tag, site_aliases.alias AS alias FROM sites
			JOIN site_aliases
			ON sites.id = site_aliases.site_id
		) AS sites
		GROUP BY tag
	) AS sites
) AS sites

EOT;
		@list($_users, $usersCount) = Helpers::getGridData(
				User::with('sites.aliases')
				->join('site_user', 'users.id', '=', 'site_user.user_id')
				->join(DB::raw($sql), function($join)
				{
					$join->on('site_user.site_id', '=', 'sites.id');
				})
				
				->select(array('users.id as id', 'users.username as username', 'users.name as name', 'sites.sites as sites'))
			);
			
//Debugbar::info($_users->toArray());
		$users = array();
		$sites = array();
		
		foreach ($_users->toArray() as $_user) {
			
			foreach ($_user['sites'] as $_site) {
				
				
				$aliases = array(0 => 'dummy');
				
				foreach ($_site['aliases'] as $_alias) {
					if ($_alias['server_name']) {
					$aliases[0] = '*'.$_alias['alias'].':'.$_alias['port'];
					} else {
						$aliases[] = $_alias['alias'].':'.$_alias['port'];
					}
				}
				
				$sites[] = sprintf('%s ( %s )', $_site['tag'], implode(', ', $aliases));
			}
			
			$users[] = array(
				'id' => $_user['id'],
				'username' => $_user['username'],
				'name' => $_user['name'],
				//'activated' => $_user['activated'],
				
				'sites' => implode(', ', $sites),
			);
		}

		$usersCount = User::count();
		return array('data' => $users, 'total' => $usersCount);
	}
}