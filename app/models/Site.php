<?php

class Site extends \Eloquent {
	protected $fillable = [];
	
	protected $table = 'sites';
	
	// private $validationRules = array(
		// 'alias' => 'required|between:3,127|custom.domain|unique_with:site_aliases,port',
		// 'port'  => 'required|integer|between:80,49151',
	// );
	
	private static $validator;
	private static $validationMessage;
	
	
	public function updateValidationPasses() {
		Input::merge(array(
			'tag'   => $this->tag,
			'aliases' => preg_replace('#[\r\n]+#', "\r\n", Input::get('aliases')),
		));
	
		$validationRules = array(
			'alias' => 'required|between:3,127|custom.domain',
			'port'  => 'required|integer|between:80,49151',
		);
		
		$currentServerName = $this->aliases()->where('server_name', '=', 1)->first();
		
		@list($serverName, $port) = explode(':', Input::get('server_name'));
		
		if (sprintf('%s:%s', $serverName, $port) != sprintf('%s:%s', $currentServerName['alias'], $currentServerName['port'])) {
			Input::merge(array(
				'alias' => $serverName,
				'port'  => $port,
			));
			
			$v = Validator::make(Input::all(),
								$validationRules,
								array(
										'custom.domain' => ':attribute is not valid.',
								)
							);
			$v->setAttributeNames(array(
								'alias' => '"Sever Name"',
								'port'  => '"Port"',
								)
			);
		
			if ($v->fails()) {
				$this->validator = $v;
				$this->validationMessage = $v->messages()->first();
				return false;
			}
		
		}
		
		
	
	}
	
	public function updateValidationFails() {
		return ( ! $this->updateValidationPasses());
	}
	
	
	public function validationPasses() {
		$tag = OS::getNextSiteTag();
		
		Input::merge(array(
			'tag'   => $tag,
			'aliases' => preg_replace('#[\r\n]+#', "\r\n", Input::get('aliases')),
		));
		
		$validationRules = array(
			'alias' => 'required|between:3,127|custom.domain|unique_with:site_aliases,port',
			'port'  => 'required|integer|between:80,49151',
		);
		
		@list($serverName, $port) = explode(':', Input::get('server_name'));
	
		Input::merge(array(
			
			'alias' => $serverName,
			'port'  => $port,
		));
		
		$v = Validator::make(Input::all(),
							$validationRules,
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
							$validationRules,
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
	
	
	
	// public function setValidationRules(array $newRules)	{
		// $this->validationRules = array_replace($this->validationRules, $newRules);
	// }
	
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
	
	
	
	public static function getValidator() {
		return self::$validator;
	}
	
	public static function getValidationMessage() {
		return self::$validationMessage;
	}
	
	private static function validateAddSite() {
		$tag = OS::getNextSiteTag();
		
		Input::merge(array(
			'tag'   => $tag,
			'aliases' => preg_replace('#[\r\n]+#', "\r\n", Input::get('aliases')),
		));
		
		$validationRules = array(
			'tag' => 'required|unique:sites,tag',
			'alias' => 'required|between:3,127|custom.domain|unique_with:site_aliases,port',
			'port'  => 'required|integer|between:80,49151',
		);
		
		$serverName = Input::get('server_name');
		$port = Input::get('port');
	
		Input::merge(array(
			'alias' => $serverName,
		));
		
		$v = Validator::make(Input::all(),
							$validationRules,
							array(
									'custom.domain' => ':attribute is not valid.',
									'unique_with' => 'This combination of :fields already exists.',
							)
						);
		$v->setAttributeNames(array(
							'tag'   => '"Server Tag"',
							'alias' => '"Sever Name"',
							'port'  => '"Port"',
							)
		);
		
		if ($v->fails()) {
			self::$validator = $v;
			
			$fail = $v->failed();
			if (isset($fail['alias']['Unique_with'])) {
				self::$validationMessage = sprintf('"%s:%s" is already taken.', $serverName, $port);
			} else {
				self::$validationMessage = $v->messages()->first();
			}
			
			return false;
		} else {
			$aliases = explode("\r\n", trim(Input::get('aliases')));
			
			foreach ($aliases as $alias) {
				if ( ! $alias) { continue; }
				
				Input::merge(array(
					'alias' => $alias,
				));
				
				$v = Validator::make(Input::all(),
							$validationRules,
							array(
									'custom.domain' => ':attribute is not valid.',
									'unique_with' => 'This combination of :fields already exists.',
							)
						);
				$v->setAttributeNames(array(
									'alias' => '"Alias"',
									)
				);
		
				if ($v->fails()) {
					self::$validator = $v;
					
					$fail = $v->failed();
					if (isset($fail['alias']['Unique_with'])) {
						self::$validationMessage = sprintf('"%s:%s" is already taken.', $alias, $port);
					} else {
						self::$validationMessage = $v->messages()->first();
					}
					
					return false;
				}
			}
		}
		
		return true;
	}
	
	private static function validateUpdateSite($site) {
		Input::merge(array(
			'aliases' => preg_replace('#[\r\n]+#', "\r\n", Input::get('aliases')),
		));
		
		$validationRules = array(
			'alias' => 'required|between:3,127|custom.domain|unique_with:site_aliases,port',
			'port'  => 'required|integer|between:80,49151',
		);
		
		$serverName = Input::get('server_name');
		$port = Input::get('port');
	
		Input::merge(array(
			'alias' => $serverName,
		));
		
		$v = Validator::make(Input::all(),
							$validationRules,
							array(
									'custom.domain' => ':attribute is not valid.',
									'unique_with' => 'This combination of :fields already exists.',
							)
						);
		$v->setAttributeNames(array(
							'tag'   => '"Server Tag"',
							'alias' => '"Sever Name"',
							'port'  => '"Port"',
							)
		);
	
	
	}
	
	public static function updateSite($site) {
		
		if ( ! Site::validateUpdateSite($site)) {
			return false;
		}
		
		$tag = $site->tag;
		$aliases = $site->aliases()->get();
		@list($serverName, $port) = explode(':', Input::get('server_name'));
		
		if (OS::updateSite($tag, $serverName, $port, $aliases)) {
			Alias::where('site_id', '=', $site->id)->delete();
			
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
				
				if (OS::addAlias($tag, $_serverName, $_port)) {
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
	
	public static function updateServerName($site) {
		$siteTag = $site->tag;
		$oldServerName = $site->aliases()->where('server_name', '=', 1)->pluck('alias');
		$newServerName = Input::get('server_name');
		$port = $site->aliases()->where('server_name', '=', 1)->pluck('port');
		
		Input::merge(array('alias' => Input::get('server_name'), 'port' => $port));
		
		$validationRules = array('alias' => 'required|between:3,127|custom.domain|unique_with:site_aliases,port,'.$site->aliases()->where('server_name', '=', 1)->pluck('id'));

		$v = Validator::make(Input::all(), $validationRules);
		$v->setAttributeNames(array('server_name'  => '"Server Name"'));
		
		if ($v->fails()) {
			self::$validator = $v;
			self::$validationMessage = $v->messages()->first();
			return false;
		}
		
		//////////////////////////
		
		if (OS::updateServerName($siteTag, $oldServerName, $newServerName, $port)) {
			$serverName = Alias::where('site_id', '=', $site->id)->where('server_name', '=', 1)->first();
			$serverName->alias = $newServerName;
			$serverName->save();
			
		} else {
			self::$validationMessage = 'Unable to update "Server Name".';
			return false;
		}
		
		return true;
	}
	
	public static function updatePort($site) {
		$siteTag = $site->tag;
		$serverName = $site->aliases()->where('server_name', '=', 1)->pluck('alias');
		$oldPort = $site->aliases()->where('server_name', '=', 1)->pluck('port');
		$newPort = Input::get('port');
		
		$validationRules = array(
			'port'  => 'required|integer|between:80,49151',
		);

		$v = Validator::make(Input::all(), $validationRules);
		$v->setAttributeNames(array('port'  => '"Port"'));
		
		if ($v->fails()) {
			self::$validator = $v;
			self::$validationMessage = $v->messages()->first();
			return false;
		}
		
		$aliases = $site->aliases()->get();
		foreach ($aliases as $alias) {
			if (Alias::where('alias', '=', $alias->alias)->where('port', '=', $newPort)->first()) {
				self::$validationMessage = sprintf('"%s:%s" already exists.', $alias['alias'], $newPort);
				return false;
			}
		}
		
		//////////////////////////
		
		if (OS::updatePort($siteTag, $serverName, $oldPort, $newPort)) {
			$aliases = Alias::where('site_id', '=', $site->id)->get();
			foreach ($aliases as $alias) {
				$alias->port = $newPort;
				$alias->save();
			}
		} else {
			self::$validationMessage = 'Unable to update "Port".';
			return false;
		}
		
		return true;
	}
	
	public static function addSite() {
	
		if ( ! Site::validateAddSite()) {
			return false;
		}
		
		$siteTag = Input::get('tag');
		$serverName = Input::get('server_name');
		$port = Input::get('port');
		
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

			$aliases = explode("\r\n", trim(Input::get('aliases')));
			
			foreach ($aliases as $alias) {
				if ( ! $alias) { continue; }
				
				if (OS::addAlias($siteTag, $alias, $port)) {
					$site->aliases()->save(new Alias(array(
									'alias' => $alias,
									'port' => $port,
									'server_name' => 0,
									)
								));
				}
			}
			
			return true;
		} else {
			self::$validationMessage = 'Unable to create this site.';
			return false;
		}
	}
	
	public static function changeState($site) {
		if ($site->activated) {
			if (OS::disableSite($site->tag, $site->aliases()->where('server_name', '=', 1)->pluck('alias'), $site->aliases()->where('server_name', '=', 1)->pluck('port'))) {
				$site->activated = 0;
				$site->save();
			} else { return false; }
		} else {
			if (OS::enableSite($site->tag, $site->aliases()->where('server_name', '=', 1)->pluck('alias'), $site->aliases()->where('server_name', '=', 1)->pluck('port'))) {
				$site->activated = 1;
				$site->save();
			} else { return false; }
		}
		
		return true;
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
					$aliases[0] = sprintf('<span style="color:red;">%s</span>', $alias['alias'].':'.$alias['port']);
				} else {
					$aliases[] = $alias['alias'].':'.$alias['port'];
				}
			}
			
			$sites[] = array(
				'id' => $site['id'],
				'activated' => sprintf('<a href="%s" class="activated">%s</a>', route('sites.change-state', array('site' => $site['id'])), $site['activated'] ? 'Yes' : 'No'),
				'tag' => sprintf('<a href="%s">%s</a>', route('sites.get-details', array('site' => $site['id'])), $site['tag']),
				'alias' => implode(', ', $aliases),
			);
		}

		$sitesCount = Site::count();
		return array('data' => $sites, 'total' => $sitesCount);
	}
	
	
}