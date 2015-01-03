<?php

class Site extends \Eloquent {
	protected $fillable = [];
	
	protected $table = 'sites';
	
	
	
	private static $validator;
	private static $validationMessage;
	
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
	public function settings() {
		// Second & third arguments are the names of foreign key and local key.
		return $this->hasMany('SiteSettings', 'site_id', 'id');
	}
	
	public static function getValidator() {
		return self::$validator;
	}
	
	public static function getValidationMessage() {
		return self::$validationMessage;
	}
	
	
	public static function addSite() {
		$serverTag = OS::getNextServerTag();
		$serverPort = Input::get('server_port');
		$serverName = strtolower(Input::get('server_name'));
		$serverAliases = Helpers::sanitizeAliasesArray(sprintf("%s\r\n%s", $serverName, Input::get('server_aliases')));
		$serverQuota = Input::get('server_quota');
		
		Input::merge(array(
			'server_tag'   => $serverTag,
			'server_aliases' => $serverAliases,
			'server_name' => $serverName, 
		));
		
		// have to check uniqueness myself
		$validationRules = array(
			'server_port'  => 'required|integer|between:80,49151',
			'server_name' => 'required|between:3,127|custom.domain',
			'server_quota' => 'required|integer|min:10'
		);
		
		$v = Validator::make(Input::all(), $validationRules, array('custom.domain' => "The :attribute ($serverName) is not valid.",));
		$v->setAttributeNames(array('server_name' => 'Server Name', 'server_port'  => 'Server Port', 'server_quota'  => 'Server Quota'));
		
		if ($v->fails()) {
			self::$validator = $v;
			self::$validationMessage = $v->messages()->first();
			return false;
		}
		
		// checking server_aliases
		foreach ($serverAliases as $serverAlias) {
			// have to check uniqueness myself
			$validationRules = array('server_alias' => 'required|between:3,127|custom.domain',);
			$v = Validator::make(array('server_alias' => $serverAlias), $validationRules, array('custom.domain' => "The :attribute ($serverAlias) is not valid.",));
			$v->setAttributeNames(array('server_alias' => 'Server Alias'));
			
			if ($v->fails()) {
				self::$validator = $v;
				self::$validationMessage = $v->messages()->first();
				return false;
			}
			
			// checking server_aliases and server_port for uniqueness
			if (
				SiteSettings::where('setting_name', '=', 'server_port_server_aliases')->where(function ($query) use ($serverPort, $serverAlias) {
					$query->where('setting_value', 'LIKE', sprintf('%s.%s %%', $serverPort, $serverAlias))->orWhere('setting_value', 'LIKE', sprintf('%% %s.%s %%', $serverPort, $serverAlias))->orWhere('setting_value', 'LIKE', sprintf('%% %s.%s', $serverPort, $serverAlias));
				})->first()
			) {
				self::$validator = null;
				self::$validationMessage = "Server Alias/Name ($serverAlias:$serverPort) is already taken.";
				return false;
			}
		}
		
		////////////////////
		
		$serverSettings = Config::get('panel.server_settings');
		$serverSettings['activated'] = 'yes';
		$serverSettings['server_tag'] = $serverTag;
		$serverSettings['server_port'] = $serverPort;
		$serverSettings['server_name'] = $serverName;
		$serverSettings['server_quota'] = $serverQuota;
		$serverSettings['server_aliases'] = implode(' ', $serverAliases);
		$serverSettings['server_port_server_aliases'] = preg_replace('#(\S+)#', $serverSettings['server_port'].'.$1', $serverSettings['server_aliases']);
		$serverSettings['mod_page_speed_domains'] = preg_replace('#(\S+)#', 'ModPagespeedDomain *$1'."\r\n    ", $serverSettings['server_aliases']);;
		
		
		$site = new Site();
		$site->tag = $serverSettings['server_tag'];
		$site->activated = $serverSettings['activated'];
		$site->aliases = $serverSettings['server_aliases'];
		$site->save();
		$siteId = $site->id;
		
		foreach ($serverSettings as $settingName => $settingValue) {
			$siteSetting = new SiteSettings();
			$siteSetting->site_id = $siteId;
			$siteSetting->setting_name = $settingName;
			$siteSetting->setting_value = $settingValue;
			$siteSetting->save();
		}
		
		$serverTemplates = DB::table('site_templates')->lists('content', 'type');
		
		$allServerAliases  = DB::table('site_settings')->where('setting_name', '=', 'server_aliases')->get();
		$hosts = array();
		foreach ($allServerAliases as $serverAliases) {
			$serverAliases = explode(' ', $serverAliases->setting_value);
			$hosts = array_merge($hosts, $serverAliases);
		}
		$hosts = sprintf('# webpanel%s127.0.0.1 %s', "\r\n", implode(' ', array_unique(array_filter(array_map('trim', $hosts)))));
		$serverSettings['hosts'] = $hosts;
		
		$allPorts   = DB::table('site_settings')->where('setting_name', '=', 'server_port')->groupBy('setting_value')->lists('setting_value');
		$defaultServer = '';
		
		if ($allPorts) {
			foreach ($allPorts as $port) {
				$defaultServer .= sprintf('listen %s default_server;', $port);
			}
		}
		$serverSettings['default_server'] = $defaultServer;
		
		if ( ! OS::addSite($serverSettings, $serverTemplates)) {
			// do we want to rollback the db?
			self::$validator = null;
			self::$validationMessage = sprintf('Unable to create this site.<pre>%s</pre>', OS::$errorMessage);
			return false;
		}
		
		return true;
		
	
	
	}
	
	public static function removeSite($site) {
		$serverTag = $site->settings()->where('setting_name', '=', 'server_tag')->pluck('setting_value');
		
		// $alias = $site->aliases()->where('server_name', '=', 1)->first();
		// $serverName = $alias->alias;
		// $port = $alias->port;
		
		// foreach ($site->aliases()->get() as $alias) {
			// OS::removeAlias($siteTag, $alias->alias, $alias->port);
		// }
		
		// if (OS::removeSite($siteTag, $serverName, $port)) {
			
			// $site->delete();
		
			// return true;
		// } else {
			// return false;
		// }
	}
	
	public static function getIndexData() {
		//Input::merge(array('sort' => Input::get('sort', array(array('field' => 'tag', 'dir' => 'asc')))));
		
		@list($_sites, $sitesCount) = Helpers::getGridData(Site::select(array('id', 'activated', 'tag', 'aliases')));
		
		//dd($_sites);
		
		// Helpers::getGridData(
									// Site::with('aliases')->join('site_aliases', 'site_aliases.site_id', '=', 'sites.id')
									// ->select(array('sites.id as id', 'sites.activated as activated', 'sites.tag as tag', 'site_aliases.alias as alias'))
									// );

		$sites = array();
		foreach ($_sites->toArray() as $site) {
			
			$sites[] = array(
				'id' => $site['id'],
				'activated' => sprintf('<a href="%s" class="activated">%s</a>', route('sites.change-state', array('site' => $site['id'])), $site['activated'] == 'yes' ? 'Yes' : 'No'),
				'tag' => sprintf('<a href="%s">%s</a>', route('sites.get-details', array('site' => $site['id'])), $site['tag']),
				'alias' => $site['aliases'],
			);
		}

		//$sitesCount = Site::count();
		return array('data' => $sites, 'total' => $sitesCount);
	}
}