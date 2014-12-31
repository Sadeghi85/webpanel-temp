<?php

class OS {

	public static function getNextServerTag() {
		$serverTagsDir = sprintf('%s/sites-available', Config::get('panel.web_base_dir'));
		
		$serverTags = shell_exec("sudo ls \"$serverTagsDir\" | grep -P ^web[0-9]{3}$ | sed -e\"s/web0*//\"");
		$serverTags = explode("\n", $serverTags);
		
		$nextServerTag = 'web'.str_pad(min(array_diff(range(1, 999), $serverTags)), 3, '0', STR_PAD_LEFT);
		
		return $nextServerTag;
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
	
	public static function removeSite($siteTag, $serverName, $port) {
		$panelCommandsPath = Config::get('panel.panel_commands_path');
		
		exec("sudo $panelCommandsPath/domaindel.sh $siteTag $serverName $port", $output, $statusCode);
		
		if ($statusCode == 0) {
			return true;
		} else {
			return false;
		}
	}
	
	public static function addAlias ($siteTag, $alias, $port) {
		$panelCommandsPath = Config::get('panel.panel_commands_path');
		
		exec("sudo $panelCommandsPath/aliasdef.sh $siteTag $alias $port", $output, $statusCode);
		
		if ($statusCode == 0) {
			return true;
		} else {
			return false;
		}
	}
	
	public static function removeAlias($siteTag, $alias, $port) {
		$panelCommandsPath = Config::get('panel.panel_commands_path');
		
		exec("sudo $panelCommandsPath/aliasdel.sh $siteTag $alias $port", $output, $statusCode);
		
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
	
	public static function updatePort($siteTag, $serverName, $oldPort, $newPort) {
		$panelCommandsPath = Config::get('panel.panel_commands_path');
		
		exec("sudo $panelCommandsPath/reset_port.sh $siteTag $serverName $oldPort $newPort", $output, $statusCode);
		
		if ($statusCode == 0) {
			return true;
		} else {
			return false;
		}
	}
	
	public static function updateServerName($siteTag, $oldServerName, $newServerName, $port) {
		$panelCommandsPath = Config::get('panel.panel_commands_path');
		
		exec("sudo $panelCommandsPath/reset_servername.sh $siteTag $oldServerName $newServerName $port", $output, $statusCode);
		
		if ($statusCode == 0) {
			return true;
		} else {
			return false;
		}
	}
	
	// public static function updateSite($siteTag, $serverName, $port, $aliases) {
		// $panelCommandsPath = Config::get('panel.panel_commands_path');
		
		// foreach ($aliases as $alias) {
			// OS::removeAlias($siteTag, $alias->alias, $alias->port);
		// }
		
		// exec("sudo $panelCommandsPath/reset_servername.sh $siteTag $serverName $port", $output, $statusCode);
		
		// if ($statusCode == 0) {
			// return true;
		// } else {
			// return false;
		// }
	// }
}