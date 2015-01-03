<?php

class OS {

	public static $errorMessage;
	
	public static function getNextServerTag() {
		$serverTagsDir = sprintf('%s/sites-available', Config::get('panel.web_base_dir'));
		
		$serverTags = shell_exec("sudo ls \"$serverTagsDir\" | grep -P ^web[0-9]{3}$ | sed -e\"s/web0*//\"");
		$serverTags = explode("\n", $serverTags);
		
		$nextServerTag = 'web'.str_pad(min(array_diff(range(1, 999), $serverTags)), 3, '0', STR_PAD_LEFT);
		
		return $nextServerTag;
	}
	
	public static function addSite(array $serverSettings, array $serverTemplates) {
		
		self::$errorMessage = '';
		
		$serverTag    = $serverSettings['server_tag'];
		$serverPort   = $serverSettings['server_port'];
		$serverName   = $serverSettings['server_name'];
		
		// index
		$serverTagDir = sprintf('%s/sites-available/%s', $serverSettings['web_base_dir'], $serverTag);
		$serverTagEnabledDir = sprintf('%s/sites-enabled/%s.%s', $serverSettings['web_base_dir'], $serverPort, $serverName);
		$serverTagForHumansDir = sprintf('%s/sites-available-for-humans/%s', $serverSettings['web_base_dir'], $serverTag);
		$serverTagEnabledForHumansDir = sprintf('%s/sites-enabled-for-humans/%s.%s', $serverSettings['web_base_dir'], $serverPort, $serverName);
		
		// php-fpm
		$phpfpmDir = sprintf('/etc/php-fpm.d/settings/sites-available/%s', $serverTag);
		$phpfpmEnabledDir = sprintf('/etc/php-fpm.d/settings/sites-enabled/%s.%s', $serverPort, $serverName);
		$phpfpmForHumansDir = sprintf('/etc/php-fpm.d/settings/sites-available-for-humans/%s', $serverTag);
		$phpfpmEnabledForHumansDir = sprintf('/etc/php-fpm.d/settings/sites-enabled-for-humans/%s.%s', $serverPort, $serverName);
		
		// apache
		$apacheDir = sprintf('/etc/httpd/settings/sites-available/%s', $serverTag);
		$apacheEnabledDir = sprintf('/etc/httpd/settings/sites-enabled/%s.%s', $serverPort, $serverName);
		$apacheForHumansDir = sprintf('/etc/httpd/settings/sites-available-for-humans/%s', $serverTag);
		$apacheEnabledForHumansDir = sprintf('/etc/httpd/settings/sites-enabled-for-humans/%s.%s', $serverPort, $serverName);
		
		// nginx
		$nginxDir = sprintf('/etc/nginx/settings/sites-available/%s', $serverTag);
		$nginxEnabledDir = sprintf('/etc/nginx/settings/sites-enabled/%s.%s', $serverPort, $serverName);
		$nginxForHumansDir = sprintf('/etc/nginx/settings/sites-available-for-humans/%s', $serverTag);
		$nginxEnabledForHumansDir = sprintf('/etc/nginx/settings/sites-enabled-for-humans/%s.%s', $serverPort, $serverName);
		
		// webalizer
		$webalizerDir = sprintf('/etc/webalizer.d/settings/sites-available/%s', $serverTag);
		$webalizerEnabledDir = sprintf('/etc/webalizer.d/settings/sites-enabled/%s.%s', $serverPort, $serverName);
		$webalizerForHumansDir = sprintf('/etc/webalizer.d/settings/sites-available-for-humans/%s', $serverTag);
		$webalizerEnabledForHumansDir = sprintf('/etc/webalizer.d/settings/sites-enabled-for-humans/%s.%s', $serverPort, $serverName);
		
		// check if server_tag directory already exists
		exec(sprintf('sudo ls %s', $serverTagDir), $output, $statusCode);
		if ($statusCode == 0) {
			self::$errorMessage .= implode("\r\n", $output)."\r\n";
			return false;
		}
		
		// creating session directory for this server_tag
		exec(sprintf('sudo mkdir -p /var/lib/php/session/%s', $serverTag), $output, $statusCode);
		if ($statusCode != 0) {
			self::$errorMessage .= implode("\r\n", $output)."\r\n";
			return false;
		}
		
		// creating web root directory for this server_tag
		exec(sprintf('sudo mkdir -p %s/%s', $serverTagDir, $serverSettings['web_root_dir']), $output, $statusCode);
		if ($statusCode != 0) {
			self::$errorMessage .= implode("\r\n", $output)."\r\n";
			return false;
		}
		
		// symlinks
		exec(sprintf('sudo ln -fs "../sites-available/%s/" "%s"', $serverTag, $serverTagEnabledDir), $output, $statusCode); // sites-enabled
		exec(sprintf('sudo ln -fs "../sites-available/%s/" "%s"', $serverTag, $serverTagForHumansDir), $output, $statusCode); // sites-available-for-humans
		exec(sprintf('sudo ln -fs "../sites-available/%s/" "%s"', $serverTag, $serverTagEnabledForHumansDir), $output, $statusCode); // sites-enabled-for-humans
		
		// creating server_tag linux user
		self::addUser($serverTag, $serverTagDir, 'apache', sprintf('%s.%s', $serverPort, $serverName));
		
		// copying default index page
		$indexPageContent = preg_replace('#\{\{\s+server_tag\s+\}\}#i', $serverTag, $serverTemplates['index']);
		self::createFileFromContent(sprintf('%s/%s/index.php', $serverTagDir, $serverSettings['web_root_dir']), $indexPageContent);
		
		// creating php-fpm config
		exec(sprintf('sudo mv %s.conf %s.conf.bak', $phpfpmDir, $phpfpmDir), $output, $statusCode);
		
		$phpfpmConfigContent = preg_replace('#\{\{\s+server_tag\s+\}\}#i', $serverTag, $serverTemplates['phpfpm']);
		$phpfpmConfigContent = preg_replace('#\{\{\s+max_children\s+\}\}#i', $serverSettings['max_children'], $phpfpmConfigContent);
		$phpfpmConfigContent = preg_replace('#\{\{\s+start_servers\s+\}\}#i', $serverSettings['start_servers'], $phpfpmConfigContent);
		$phpfpmConfigContent = preg_replace('#\{\{\s+min_spare_servers\s+\}\}#i', $serverSettings['min_spare_servers'], $phpfpmConfigContent);
		$phpfpmConfigContent = preg_replace('#\{\{\s+max_spare_servers\s+\}\}#i', $serverSettings['max_spare_servers'], $phpfpmConfigContent);
		$phpfpmConfigContent = preg_replace('#\{\{\s+max_requests\s+\}\}#i', $serverSettings['max_requests'], $phpfpmConfigContent);
		$phpfpmConfigContent = preg_replace('#\{\{\s+request_terminate_timeout\s+\}\}#i', $serverSettings['request_terminate_timeout'], $phpfpmConfigContent);
		$phpfpmConfigContent = preg_replace('#\{\{\s+request_slowlog_timeout\s+\}\}#i', $serverSettings['request_slowlog_timeout'], $phpfpmConfigContent);
		$phpfpmConfigContent = preg_replace('#\{\{\s+web_base_dir\s+\}\}#i', $serverSettings['web_base_dir'], $phpfpmConfigContent);
		$phpfpmConfigContent = preg_replace('#\{\{\s+post_max_size\s+\}\}#i', $serverSettings['post_max_size'], $phpfpmConfigContent);
		$phpfpmConfigContent = preg_replace('#\{\{\s+upload_max_filesize\s+\}\}#i', $serverSettings['upload_max_filesize'], $phpfpmConfigContent);
		$phpfpmConfigContent = preg_replace('#\{\{\s+max_file_uploads\s+\}\}#i', $serverSettings['max_file_uploads'], $phpfpmConfigContent);
		$phpfpmConfigContent = preg_replace('#\{\{\s+memory_limit\s+\}\}#i', $serverSettings['memory_limit'], $phpfpmConfigContent);
		$phpfpmConfigContent = preg_replace('#\{\{\s+max_execution_time\s+\}\}#i', $serverSettings['max_execution_time'], $phpfpmConfigContent);
		$phpfpmConfigContent = preg_replace('#\{\{\s+error_reporting\s+\}\}#i', $serverSettings['error_reporting'], $phpfpmConfigContent);
		$phpfpmConfigContent = preg_replace('#\{\{\s+max_input_time\s+\}\}#i', $serverSettings['max_input_time'], $phpfpmConfigContent);
		$phpfpmConfigContent = preg_replace('#\{\{\s+default_socket_timeout\s+\}\}#i', $serverSettings['default_socket_timeout'], $phpfpmConfigContent);
		$phpfpmConfigContent = preg_replace('#\{\{\s+date_timezone\s+\}\}#i', $serverSettings['date_timezone'], $phpfpmConfigContent);
		$phpfpmConfigContent = preg_replace('#\{\{\s+output_buffering\s+\}\}#i', $serverSettings['output_buffering'], $phpfpmConfigContent);
		
		self::createFileFromContent(sprintf('%s.conf', $phpfpmDir), $phpfpmConfigContent);
		
		// symlinks
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf"', $serverTag, $phpfpmEnabledDir), $output, $statusCode); // sites-enabled
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf"', $serverTag, $phpfpmForHumansDir), $output, $statusCode); // sites-available-for-humans
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf"', $serverTag, $phpfpmEnabledForHumansDir), $output, $statusCode); // sites-enabled-for-humans
		
		
		// creating apache config
		exec(sprintf('sudo mv %s.conf %s.conf.bak', $apacheDir, $apacheDir), $output, $statusCode);
		
		$apacheConfigContent = preg_replace('#\{\{\s+server_tag\s+\}\}#i', $serverTag, $serverTemplates['apache']);
		$apacheConfigContent = preg_replace('#\{\{\s+web_base_dir\s+\}\}#i', $serverSettings['web_base_dir'], $apacheConfigContent);
		$apacheConfigContent = preg_replace('#\{\{\s+web_root_dir\s+\}\}#i', $serverSettings['web_root_dir'], $apacheConfigContent);
		$apacheConfigContent = preg_replace('#\{\{\s+server_port\s+\}\}#i', $serverSettings['server_port'], $apacheConfigContent);
		$apacheConfigContent = preg_replace('#\{\{\s+server_name\s+\}\}#i', $serverSettings['server_name'], $apacheConfigContent);
		$apacheConfigContent = preg_replace('#\{\{\s+server_port_server_aliases\s+\}\}#i', $serverSettings['server_port_server_aliases'], $apacheConfigContent);
		$apacheConfigContent = preg_replace('#\{\{\s+mod_page_speed_domains\s+\}\}#i', $serverSettings['mod_page_speed_domains'], $apacheConfigContent);
		
		self::createFileFromContent(sprintf('%s.conf', $apacheDir), $apacheConfigContent);
		
		// symlinks
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf"', $serverTag, $apacheEnabledDir), $output, $statusCode); // sites-enabled
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf"', $serverTag, $apacheForHumansDir), $output, $statusCode); // sites-available-for-humans
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf"', $serverTag, $apacheEnabledForHumansDir), $output, $statusCode); // sites-enabled-for-humans
		
		// creating nginx config
		exec(sprintf('sudo mv %s.conf %s.conf.bak', $nginxDir, $nginxDir), $output, $statusCode);
		
		$nginxConfigContent = preg_replace('#\{\{\s+server_tag\s+\}\}#i', $serverTag, $serverTemplates['nginx']);
		$nginxConfigContent = preg_replace('#\{\{\s+web_base_dir\s+\}\}#i', $serverSettings['web_base_dir'], $nginxConfigContent);
		$nginxConfigContent = preg_replace('#\{\{\s+web_root_dir\s+\}\}#i', $serverSettings['web_root_dir'], $nginxConfigContent);
		$nginxConfigContent = preg_replace('#\{\{\s+server_port\s+\}\}#i', $serverSettings['server_port'], $nginxConfigContent);
		$nginxConfigContent = preg_replace('#\{\{\s+server_aliases\s+\}\}#i', $serverSettings['server_aliases'], $nginxConfigContent);
		$nginxConfigContent = preg_replace('#\{\{\s+limit_rate\s+\}\}#i', $serverSettings['limit_rate'], $nginxConfigContent);
		$nginxConfigContent = preg_replace('#\{\{\s+limit_conn\s+\}\}#i', $serverSettings['limit_conn'], $nginxConfigContent);
		
		self::createFileFromContent(sprintf('%s.conf', $nginxDir), $nginxConfigContent);
		
		// symlinks
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf"', $serverTag, $nginxEnabledDir), $output, $statusCode); // sites-enabled
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf"', $serverTag, $nginxForHumansDir), $output, $statusCode); // sites-available-for-humans
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf"', $serverTag, $nginxEnabledForHumansDir), $output, $statusCode); // sites-enabled-for-humans
		
		// creating nginx default config
		exec('sudo mv /etc/nginx/nginx_default_server.conf /etc/nginx/nginx_default_server.conf.bak', $output, $statusCode);
		$nginxDefaultConfigContent = preg_replace('#\{\{\s+default_server\s+\}\}#i', $serverSettings['default_server'], $serverTemplates['nginxdefault']);
		self::createFileFromContent('/etc/nginx/nginx_default_server.conf', $nginxDefaultConfigContent);
		
		// creating webalizer config
		exec(sprintf('sudo mv %s.conf %s.conf.bak', $webalizerDir, $webalizerDir), $output, $statusCode);
		
		$webalizerConfigContent = preg_replace('#\{\{\s+server_tag\s+\}\}#i', $serverTag, $serverTemplates['webalizer']);
		
		self::createFileFromContent(sprintf('%s.conf', $webalizerDir), $webalizerConfigContent);
		
		// symlinks
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf"', $serverTag, $webalizerEnabledDir), $output, $statusCode); // sites-enabled
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf"', $serverTag, $webalizerForHumansDir), $output, $statusCode); // sites-available-for-humans
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf"', $serverTag, $webalizerEnabledForHumansDir), $output, $statusCode); // sites-enabled-for-humans
		
		// adding server aliases to the hosts file
		// exec('sudo mv /etc/hosts /etc/hosts.bak', $output, $statusCode);
		// exec('sudo cat /etc/hosts', $output, $statusCode);
		// $hostsContent = preg_replace('!#\s*webpanel.*!is', '', implode("\r\n", $output));
		// self::createFileFromContent('/etc/hosts', $hostsContent.$serverSettings['hosts']);
		
		// setting permissions
		exec(sprintf('sudo chmod 777 /var/lib/php/session/%s', $serverTag), $output, $statusCode);
		exec(sprintf('sudo chown -R "%s:apache" %s', $serverTag, $serverTagDir), $output, $statusCode);
		exec(sprintf('sudo chmod -R 664 %s', $serverTagDir), $output, $statusCode);
		exec(sprintf('sudo chmod -R +X %s', $serverTagDir), $output, $statusCode);
		
		// reloading servers
		if ( ! self::reloadServers()) {
			return false;
		}
		
		return true;
	}
	
	public static function reloadServers() {
		exec('sudo service php-fpm start', $output, $statusCode);
		exec('sudo service httpd start', $output, $statusCode);
		exec('sudo service nginx start', $output, $statusCode);
		exec('sudo service memcached start', $output, $statusCode);
		
		if ( ! self::testServers()) {
			return false;
		}
		
		exec('sudo service php-fpm reload', $output, $statusCode);
		if ($statusCode != 0) {
			self::$errorMessage .= implode("\r\n", $output)."\r\n";
			return false;
		}
		exec('sudo service httpd reload', $output, $statusCode);
		if ($statusCode != 0) {
			self::$errorMessage .= implode("\r\n", $output)."\r\n";
			return false;
		}
		exec('sudo service nginx reload', $output, $statusCode);
		if ($statusCode != 0) {
			self::$errorMessage .= implode("\r\n", $output)."\r\n";
			return false;
		}
		
		return true;
	}
	
	public static function testServers() {
		// testing php-fpm
		exec('sudo /usr/sbin/php-fpm -t', $output, $statusCode);
		if ($statusCode != 0) {
			self::$errorMessage .= implode("\r\n", $output)."\r\n";
			return false;
		}
		
		// testing apache
		exec('sudo /usr/sbin/httpd -t', $output, $statusCode);
		if ($statusCode != 0) {
			self::$errorMessage .= implode("\r\n", $output)."\r\n";
			return false;
		}
		
		// testing nginx
		exec('sudo /usr/sbin/nginx -t', $output, $statusCode);
		if ($statusCode != 0) {
			self::$errorMessage .= implode("\r\n", $output)."\r\n";
			return false;
		}
		
		return true;
	}
	
	public static function createFileFromContent($path, $content, $append = false) {
		$descriptorspec = array(
		   0 => array('pipe', 'r'),  // stdin is a pipe that the child will read from
		);
		
		if ($append) {
			$process = proc_open(sprintf('sudo tee --append %s', $path), $descriptorspec, $pipes);
		} else {
			$process = proc_open(sprintf('sudo tee %s', $path), $descriptorspec, $pipes);
		}
		
		if (is_resource($process)) {
			// $pipes now look like this:
			// 0 => writeable handle connected to child stdin
			// 1 => readable handle connected to child stdout
			fwrite($pipes[0], $content);
			fclose($pipes[0]);
		}
	}
	
	public static function addUser($user, $home = '', $group = '', $comment = '', $shell = '/sbin/nologin') {
		$command = '';
		if ($comment) { $command .= sprintf(' --comment "%s" ', $comment); }
		if ($group) { $command .= sprintf(' -g %s ', $group); }
		if ($home) { $command .= sprintf(' --home "%s" ', $home); }
		if ($shell) { $command .= sprintf(' --shell "%s" ', $shell); }
		$command .= sprintf(' "%s"', $user);
	
		// check if user exists
		exec(sprintf('sudo id %s', $user), $output, $statusCode);
		if ($statusCode == 0) {
			// user exists
			exec(sprintf('sudo usermod %s', $command), $output, $statusCode);
		} else {
			// user doesn't exist
			exec(sprintf('sudo useradd %s', $command), $output, $statusCode);
		}
	
	}
	
	public static function enableSite($siteTag, $serverName, $port) {
		$panelCommandsPath = Config::get('panel.panel_commands_path');
		
		exec("sudo $panelCommandsPath/domainen.sh $siteTag $serverName $port", $output, $statusCode);
		
		if ($statusCode == 0) {
			return true;
		} else {
			return false;
		}
	}
	
	public static function disableSite($siteTag, $serverName, $port) {
		$panelCommandsPath = Config::get('panel.panel_commands_path');
		
		exec("sudo $panelCommandsPath/domaindis.sh $siteTag $serverName $port", $output, $statusCode);
		
		if ($statusCode == 0) {
			return true;
		} else {
			return false;
		}
	}
	
	// public static function removeUser ($username) {
		// $panelCommandsPath = Config::get('panel.panel_commands_path');
		
		// exec("sudo $panelCommandsPath/userdel.sh $username", $output, $statusCode);
		
		// if ($statusCode == 0) {
			// return true;
		// } else {
			// return false;
		// }
	// }
	
	// public static function addUser ($username, $password) {
		// $panelCommandsPath = Config::get('panel.panel_commands_path');
		
		// exec("sudo $panelCommandsPath/userdef.sh $username $password", $output, $statusCode);
		
		// if ($statusCode == 0) {
			// return true;
		// } else {
			// return false;
		// }
	// }
	
	// public static function removeSite($siteTag, $serverName, $port) {
		// $panelCommandsPath = Config::get('panel.panel_commands_path');
		
		// exec("sudo $panelCommandsPath/domaindel.sh $siteTag $serverName $port", $output, $statusCode);
		
		// if ($statusCode == 0) {
			// return true;
		// } else {
			// return false;
		// }
	// }
	
	// public static function addAlias ($siteTag, $alias, $port) {
		// $panelCommandsPath = Config::get('panel.panel_commands_path');
		
		// exec("sudo $panelCommandsPath/aliasdef.sh $siteTag $alias $port", $output, $statusCode);
		
		// if ($statusCode == 0) {
			// return true;
		// } else {
			// return false;
		// }
	// }
	
	// public static function removeAlias($siteTag, $alias, $port) {
		// $panelCommandsPath = Config::get('panel.panel_commands_path');
		
		// exec("sudo $panelCommandsPath/aliasdel.sh $siteTag $alias $port", $output, $statusCode);
		
		// if ($statusCode == 0) {
			// return true;
		// } else {
			// return false;
		// }
	// }
	
	// public static function addSite($siteTag, $serverName, $port) {
		// $panelCommandsPath = Config::get('panel.panel_commands_path');
		
		// exec("sudo $panelCommandsPath/domaindef.sh $siteTag $serverName $port", $output, $statusCode);
		
		// if ($statusCode == 0) {
			// return true;
		// } else {
			// return false;
		// }
	// }
	
	// public static function updatePort($siteTag, $serverName, $oldPort, $newPort) {
		// $panelCommandsPath = Config::get('panel.panel_commands_path');
		
		// exec("sudo $panelCommandsPath/reset_port.sh $siteTag $serverName $oldPort $newPort", $output, $statusCode);
		
		// if ($statusCode == 0) {
			// return true;
		// } else {
			// return false;
		// }
	// }
	
	// public static function updateServerName($siteTag, $oldServerName, $newServerName, $port) {
		// $panelCommandsPath = Config::get('panel.panel_commands_path');
		
		// exec("sudo $panelCommandsPath/reset_servername.sh $siteTag $oldServerName $newServerName $port", $output, $statusCode);
		
		// if ($statusCode == 0) {
			// return true;
		// } else {
			// return false;
		// }
	// }
	
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