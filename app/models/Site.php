<?php

class Site extends \Eloquent {
	protected $fillable = [];
	
	protected $table = 'sites';
	
	private $validationRules = array(
		'tag'   => 'required|unique:sites,tag',
		'alias' => 'required|between:3,127|custom.domain|unique_with:site_aliases,port',
		'port'  => 'required|integer|between:80,49151',
	);
	
	private $validator;
	private $validationMessage;
	
	public function validationPasses() {
		$siteTag = OS::getNextSiteTag();
		@list($serverName, $port) = explode(':', Input::get('server_name'));
	
		Input::merge(array(
			'tag'   => $siteTag,
			'alias' => $serverName,
			'port'  => $port,
		));
		
		$v = Validator::make(Input::all(),
							$this->validationRules,
							array(
									'custom.domain' => ':attribute is not valid.',
									'unique_with' => 'This combination of :fields already exists.',
							)
						);
		$v->setAttributeNames(array(
							'alias' => '"Sever Name"',
							'port'  => '"Port"',
							)
		);
		
		if ($v->fails()) {
			
			$this->validator = $v;
			
			$fail = $v->failed();
			if (isset($fail['alias']['Unique_with'])) {
				$this->validationMessage = sprintf('"%s" is already taken.', Input::get('server_name'));
			} else {
				$this->validationMessage = $v->messages()->first();
			}
			
			return false;
		} else {
			$aliases = explode("\r\n", trim(Input::get('aliases')));
			
			foreach ($aliases as $alias) {
				if ( ! $alias) { continue; }
				
				@list($serverName, $port) = explode(':', $alias);
				
				Input::merge(array(
					'alias' => $serverName,
					'port' => $port,
				));
				
				$v = Validator::make(Input::all(),
							$this->validationRules,
							array(
									'custom.domain' => ':attribute is not valid.',
									'unique_with' => 'This combination of :fields already exists.',
							)
						);
				$v->setAttributeNames(array(
									'alias' => '"Alias"',
									'port'  => '"Port"',
									)
				);
		
				if ($v->fails()) {
					$this->validator = $v;
					
					$fail = $v->failed();
					if (isset($fail['alias']['Unique_with'])) {
						$this->validationMessage = sprintf('"%s" is already taken.', $alias);
					} else {
						$this->validationMessage = $v->messages()->first();
					}
					
					return false;
				}
			}
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
	
	/**
	 * Many to many relationship.
	 *
	 * @return Model
	 */
	public function users() {
		// Second argument is the name of pivot table.
		// Third & forth arguments are the names of foreign keys.
		return $this->belongsToMany('User', 'site_user', 'site_id', 'user_id')->withTimestamps();
		
	}
	
	/**
	 * One to many relationship.
	 *
	 * @return Model
	 */
	public function aliases() {
		// Second & third arguments are the names of foreign key and local key.
		return $this->hasMany('Alias', 'site_id', 'id');
	}
	
	public static function removeSite($site) {
		$siteTag = $site->tag;
		
		$alias = $site->aliases()->where('server_name', '=', 1)->first();
		$serverName = $alias->alias;
		$port = $alias->port;
		
		foreach ($site->aliases()->get() as $alias) {
			OS::removeAlias($siteTag, $alias->alias, $alias->port);
		}
		
		if (OS::removeSite($siteTag, $serverName, $port)) {
			
			$site->delete();
		
			return true;
		} else {
			return false;
		}
	}
	
	public static function addSite() {
		$siteTag = OS::getNextSiteTag();
		@list($serverName, $port) = explode(':', Input::get('server_name'));
			
		if (OS::addSite($siteTag, $serverName, $port)) {
			$site = new Site;
			$site->tag = $siteTag;
			$site->activated = 1;
			
			$site->save();
			
			$site->aliases()->save(new Alias(array(
								'alias' => $serverName,
								'port' => $port,
								'server_name' => 1,
								)
							));

			$_aliases = explode("\r\n", trim(Input::get('aliases')));
			foreach ($_aliases as $_alias) {
				if ( ! $_alias) { continue; }
				
				@list($_serverName, $_port) = explode(':', $_alias);
				
				if (OS::addAlias($siteTag, $_serverName, $_port)) {
					$site->aliases()->save(new Alias(array(
									'alias' => $_serverName,
									'port' => $_port,
									'server_name' => 0,
									)
								));
				}
			}

			return true;
		} else {
			return false;
		}
	}
	
	public static function getIndexData() {
		//Input::merge(array('sort' => Input::get('sort', array(array('field' => 'tag', 'dir' => 'asc')))));
		
		@list($_sites, $sitesCount) = Helpers::getGridData(
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

		$sitesCount = Site::count();
		return array('data' => $sites, 'total' => $sitesCount);
	}
	
	
}