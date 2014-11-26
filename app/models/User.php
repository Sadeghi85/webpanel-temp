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
		
		// @list($_sites, $sitesCount) = Helpers::getGridData(
									// Site::with('aliases')->join('site_aliases', 'site_aliases.site_id', '=', 'sites.id')
									// ->select(array('sites.id as id', 'sites.activated as activated', 'sites.tag as tag', 'site_aliases.alias as alias'))
									// );

		// $sites = array();
		// foreach ($_sites->toArray() as $site) {
			// $aliases = array(0 => 'dummy');
			// foreach ($site['aliases'] as $alias) {
				// if ($alias['server_name']) {
					// $aliases[0] = '*'.$alias['alias'].':'.$alias['port'];
				// } else {
					// $aliases[] = $alias['alias'].':'.$alias['port'];
				// }
			// }
			
			// $sites[] = array(
				// 'id' => $site['id'],
				// 'activated' => $site['activated'],
				// 'tag' => $site['tag'],
				// 'alias' => implode(', ', $aliases),
			// );
		// }

		// $sitesCount = Site::count();
		// return array('data' => $sites, 'total' => $sitesCount);
	}
}