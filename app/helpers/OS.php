<?php

class OS {

	public static function getNextSiteTag() {
		$sitesPath = Config::get('panel.sites_path');
		
		$siteTags = shell_exec("sudo ls \"$sitesPath\" | grep -P ^web[0-9]{3}$ | sed -e\"s/web0*//\"");
		
		$siteTags = explode("\n", $siteTags);
		
		$nextTag = 'web'.str_pad(min(array_diff(range(1, 999), $siteTags)), 3, '0', STR_PAD_LEFT);
		
		return $nextTag;
	}
	
	public static function enableSite ($siteTag, $serverName, $port) {
		$panelCommandsPath = Config::get('panel.panel_commands_path');
		
		exec("sudo $panelCommandsPath/domainen.sh $siteTag $serverName $port", $output, $statusCode);
		
		if ($statusCode == 0) {
			return true;
		} else {
			return false;
		}
	}
	
	public static function disableSite ($siteTag, $serverName, $port) {
		$panelCommandsPath = Config::get('panel.panel_commands_path');
		
		exec("sudo $panelCommandsPath/domaindis.sh $siteTag $serverName $port", $output, $statusCode);
		
		if ($statusCode == 0) {
			return true;
		} else {
			return false;
		}
	}
	
	public static function removeUser ($username) {
		$panelCommandsPath = Config::get('panel.panel_commands_path');
		
		exec("sudo $panelCommandsPath/userdel.sh $username", $output, $statusCode);
		
		if ($statusCode == 0) {
			return true;
		} else {
			return false;
		}
	}
	
	public static function addUser ($username, $password) {
		$panelCommandsPath = Config::get('panel.panel_commands_path');
		
		exec("sudo $panelCommandsPath/userdef.sh $username $password", $output, $statusCode);
		
		if ($statusCode == 0) {
			return true;
		} else {
			return false;
		}
	}
	
	public static function removeAlias($siteTag, $serverName, $port) {
		$panelCommandsPath = Config::get('panel.panel_commands_path');
		
		exec("sudo $panelCommandsPath/aliasdel.sh $siteTag $serverName $port", $output, $statusCode);
		
		if ($statusCode == 0) {
			return true;
		} else {
			return false;
		}
	}
	
	public static function removeSite($siteTag, $serverName, $port) {
		$panelCommandsPath = Config::get('panel.panel_commands_path');
		
		exec("sudo $panelCommandsPath/domaindel.sh $siteTag $serverName $port", $output, $statusCode);
		
		if ($statusCode == 0) {
			return true;
		} else {
			return false;
		}
	}
	
	public static function addAlias ($siteTag, $serverName, $port) {
		$panelCommandsPath = Config::get('panel.panel_commands_path');
		
		exec("sudo $panelCommandsPath/aliasdef.sh $siteTag $serverName $port", $output, $statusCode);
		
		if ($statusCode == 0) {
			return true;
		} else {
			return false;
		}
	}
	
	public static function addSite($siteTag, $serverName, $port) {
		$panelCommandsPath = Config::get('panel.panel_commands_path');
		
		exec("sudo $panelCommandsPath/domaindef.sh $siteTag $serverName $port", $output, $statusCode);
		
		if ($statusCode == 0) {
			return true;
		} else {
			return false;
		}
	}
	
	public static function updateSite($siteTag, $serverName, $port, $aliases) {
		$panelCommandsPath = Config::get('panel.panel_commands_path');
		
		foreach ($aliases as $alias) {
			OS::removeAlias($siteTag, $alias->alias, $alias->port);
		}
		
		exec("sudo $panelCommandsPath/reset_servername.sh $siteTag $serverName $port", $output, $statusCode);
		
		if ($statusCode == 0) {
			return true;
		} else {
			return false;
		}
	}
}