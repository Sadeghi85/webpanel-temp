<?php

class OS
{

	public static $errorMessage;
	
	public static function getNextServerTag()
	{
		$serverTagsDir = sprintf('%s/sites-available', Config::get('panel.web_base_dir'));
		
		$serverTags = shell_exec("sudo ls \"$serverTagsDir\" | grep -P ^web[0-9]{3}$ | sed -e\"s/web0*//\"");
		$serverTags = explode("\n", $serverTags);
		
		$nextServerTag = 'web'.str_pad(min(array_diff(range(1, 999), $serverTags)), 3, '0', STR_PAD_LEFT);
		
		return $nextServerTag;
	}
	
	public static function updateSite(array $serverSettings, array $serverTemplates)
	{
		
		self::$errorMessage = '';
		
		$serverTag    = $serverSettings['server_tag'];
		$serverPort   = $serverSettings['server_port'];
		$serverName   = $serverSettings['server_name'];
		
		// index
		$serverTagDir = sprintf('%s/sites-available/%s', $serverSettings['web_base_dir'], $serverTag);
		$serverTagEnabledDir = sprintf('%s/sites-enabled/%s', $serverSettings['web_base_dir'], $serverTag);
		$serverTagForHumansDir = sprintf('%s/sites-available-for-humans/%s.%s', $serverSettings['web_base_dir'], $serverPort, $serverName);
		$serverTagEnabledForHumansDir = sprintf('%s/sites-enabled-for-humans/%s.%s', $serverSettings['web_base_dir'], $serverPort, $serverName);
		
		// php-fpm
		$phpfpmDir = sprintf('/etc/php-fpm.d/settings/sites-available/%s', $serverTag);
		$phpfpmEnabledDir = sprintf('/etc/php-fpm.d/settings/sites-enabled/%s', $serverTag);
		$phpfpmForHumansDir = sprintf('/etc/php-fpm.d/settings/sites-available-for-humans/%s.%s', $serverPort, $serverName);
		$phpfpmEnabledForHumansDir = sprintf('/etc/php-fpm.d/settings/sites-enabled-for-humans/%s.%s', $serverPort, $serverName);
		
		// apache
		$apacheDir = sprintf('/etc/httpd/settings/sites-available/%s', $serverTag);
		$apacheEnabledDir = sprintf('/etc/httpd/settings/sites-enabled/%s', $serverTag);
		$apacheForHumansDir = sprintf('/etc/httpd/settings/sites-available-for-humans/%s.%s', $serverPort, $serverName);
		$apacheEnabledForHumansDir = sprintf('/etc/httpd/settings/sites-enabled-for-humans/%s.%s', $serverPort, $serverName);
		
		// nginx
		$nginxDir = sprintf('/etc/nginx/settings/sites-available/%s', $serverTag);
		$nginxEnabledDir = sprintf('/etc/nginx/settings/sites-enabled/%s', $serverTag);
		$nginxForHumansDir = sprintf('/etc/nginx/settings/sites-available-for-humans/%s.%s', $serverPort, $serverName);
		$nginxEnabledForHumansDir = sprintf('/etc/nginx/settings/sites-enabled-for-humans/%s.%s', $serverPort, $serverName);
		
		// webalizer
		$webalizerDir = sprintf('/etc/webalizer.d/settings/sites-available/%s', $serverTag);
		$webalizerEnabledDir = sprintf('/etc/webalizer.d/settings/sites-enabled/%s', $serverTag);
		$webalizerForHumansDir = sprintf('/etc/webalizer.d/settings/sites-available-for-humans/%s.%s', $serverPort, $serverName);
		$webalizerEnabledForHumansDir = sprintf('/etc/webalizer.d/settings/sites-enabled-for-humans/%s.%s', $serverPort, $serverName);
		
		// creating session directory for this server_tag
		exec(sprintf('sudo mkdir -p /var/lib/php/session/%s 2>&1', $serverTag), $output, $statusCode);
		
		// symlinks
		exec(sprintf('sudo ln -fs "../sites-available/%s/" "%s" 2>&1', $serverTag, $serverTagForHumansDir), $output, $statusCode); // sites-available-for-humans
		if ($serverSettings['activated']) {
			exec(sprintf('sudo ln -fs "../sites-available/%s/" "%s" 2>&1', $serverTag, $serverTagEnabledDir), $output, $statusCode); // sites-enabled
			exec(sprintf('sudo ln -fs "../sites-available/%s/" "%s" 2>&1', $serverTag, $serverTagEnabledForHumansDir), $output, $statusCode); // sites-enabled-for-humans
		}
		
		// creating server_tag linux user
		self::addUser($serverTag, $serverTagDir, 'apache', sprintf('%s.%s', $serverPort, $serverName));
		
		// creating php-fpm config
		exec(sprintf('sudo mv %s.conf %s.conf.bak 2>&1', $phpfpmDir, $phpfpmDir), $output, $statusCode);
		
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
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf" 2>&1', $serverTag, $phpfpmForHumansDir), $output, $statusCode); // sites-available-for-humans
		if ($serverSettings['activated']) {
			exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf" 2>&1', $serverTag, $phpfpmEnabledDir), $output, $statusCode); // sites-enabled
			exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf" 2>&1', $serverTag, $phpfpmEnabledForHumansDir), $output, $statusCode); // sites-enabled-for-humans
		}
		
		// creating apache config
		exec(sprintf('sudo mv %s.conf %s.conf.bak 2>&1', $apacheDir, $apacheDir), $output, $statusCode);
		
		$apacheConfigContent = preg_replace('#\{\{\s+server_tag\s+\}\}#i', $serverTag, $serverTemplates['apache']);
		$apacheConfigContent = preg_replace('#\{\{\s+web_base_dir\s+\}\}#i', $serverSettings['web_base_dir'], $apacheConfigContent);
		$apacheConfigContent = preg_replace('#\{\{\s+web_root_dir\s+\}\}#i', $serverSettings['web_root_dir'], $apacheConfigContent);
		$apacheConfigContent = preg_replace('#\{\{\s+server_port\s+\}\}#i', $serverSettings['server_port'], $apacheConfigContent);
		$apacheConfigContent = preg_replace('#\{\{\s+server_name\s+\}\}#i', $serverSettings['server_name'], $apacheConfigContent);
		$apacheConfigContent = preg_replace('#\{\{\s+server_port_server_aliases\s+\}\}#i', $serverSettings['server_port_server_aliases'], $apacheConfigContent);
		$apacheConfigContent = preg_replace('#\{\{\s+mod_page_speed_domains\s+\}\}#i', $serverSettings['mod_page_speed_domains'], $apacheConfigContent);
		
		self::createFileFromContent(sprintf('%s.conf', $apacheDir), $apacheConfigContent);
		
		// symlinks
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf" 2>&1', $serverTag, $apacheForHumansDir), $output, $statusCode); // sites-available-for-humans
		if ($serverSettings['activated']) {
			exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf" 2>&1', $serverTag, $apacheEnabledDir), $output, $statusCode); // sites-enabled
			exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf" 2>&1', $serverTag, $apacheEnabledForHumansDir), $output, $statusCode); // sites-enabled-for-humans
		}
		
		// creating nginx config
		exec(sprintf('sudo mv %s.conf %s.conf.bak 2>&1', $nginxDir, $nginxDir), $output, $statusCode);
		
		$nginxConfigContent = preg_replace('#\{\{\s+server_tag\s+\}\}#i', $serverTag, $serverTemplates['nginx']);
		$nginxConfigContent = preg_replace('#\{\{\s+web_base_dir\s+\}\}#i', $serverSettings['web_base_dir'], $nginxConfigContent);
		$nginxConfigContent = preg_replace('#\{\{\s+web_root_dir\s+\}\}#i', $serverSettings['web_root_dir'], $nginxConfigContent);
		$nginxConfigContent = preg_replace('#\{\{\s+server_port\s+\}\}#i', $serverSettings['server_port'], $nginxConfigContent);
		$nginxConfigContent = preg_replace('#\{\{\s+server_aliases\s+\}\}#i', $serverSettings['server_aliases'], $nginxConfigContent);
		$nginxConfigContent = preg_replace('#\{\{\s+limit_rate\s+\}\}#i', $serverSettings['limit_rate'], $nginxConfigContent);
		$nginxConfigContent = preg_replace('#\{\{\s+limit_conn\s+\}\}#i', $serverSettings['limit_conn'], $nginxConfigContent);
		
		self::createFileFromContent(sprintf('%s.conf', $nginxDir), $nginxConfigContent);
		
		// symlinks
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf" 2>&1', $serverTag, $nginxForHumansDir), $output, $statusCode); // sites-available-for-humans
		if ($serverSettings['activated']) {
			exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf" 2>&1', $serverTag, $nginxEnabledDir), $output, $statusCode); // sites-enabled
			exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf" 2>&1', $serverTag, $nginxEnabledForHumansDir), $output, $statusCode); // sites-enabled-for-humans
		}
		
		// creating nginx default config
		exec('sudo mv /etc/nginx/nginx_default_server.conf /etc/nginx/nginx_default_server.conf.bak 2>&1', $output, $statusCode);
		$nginxDefaultConfigContent = preg_replace('#\{\{\s+default_server\s+\}\}#i', $serverSettings['default_server'], $serverTemplates['nginxdefault']);
		self::createFileFromContent('/etc/nginx/nginx_default_server.conf', $nginxDefaultConfigContent);
		
		// creating webalizer config
		exec(sprintf('sudo mv %s.conf %s.conf.bak 2>&1', $webalizerDir, $webalizerDir), $output, $statusCode);
		
		$webalizerConfigContent = preg_replace('#\{\{\s+server_tag\s+\}\}#i', $serverTag, $serverTemplates['webalizer']);
		
		self::createFileFromContent(sprintf('%s.conf', $webalizerDir), $webalizerConfigContent);
		
		// symlinks
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf" 2>&1', $serverTag, $webalizerForHumansDir), $output, $statusCode); // sites-available-for-humans
		if ($serverSettings['activated']) {
			exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf" 2>&1', $serverTag, $webalizerEnabledDir), $output, $statusCode); // sites-enabled
			exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf" 2>&1', $serverTag, $webalizerEnabledForHumansDir), $output, $statusCode); // sites-enabled-for-humans
		}
		
		// adding server aliases to the hosts file
		// exec('sudo mv /etc/hosts /etc/hosts.bak 2>&1', $output, $statusCode);
		// exec('sudo cat /etc/hosts 2>&1', $output, $statusCode);
		// $hostsContent = preg_replace('!#\s*webpanel.*!is', '', implode("\r\n", $output));
		// self::createFileFromContent('/etc/hosts', $hostsContent.$serverSettings['hosts']);
		
		// setting permissions
		exec(sprintf('sudo chmod 777 /var/lib/php/session/%s 2>&1', $serverTag), $output, $statusCode);
		exec(sprintf('sudo chown -R "%s:apache" %s 2>&1', $serverTag, $serverTagDir), $output, $statusCode);
		exec(sprintf('sudo chmod -R 664 %s 2>&1', $serverTagDir), $output, $statusCode);
		exec(sprintf('sudo chmod -R +X %s 2>&1', $serverTagDir), $output, $statusCode);
		
		// reloading servers
		if ( ! self::reloadServers()) {
			return false;
		}
		
		return true;
	}
	
	public static function addSite(array $serverSettings, array $serverTemplates)
	{
		
		self::$errorMessage = '';
		
		$serverTag    = $serverSettings['server_tag'];
		$serverPort   = $serverSettings['server_port'];
		$serverName   = $serverSettings['server_name'];
		
		// index
		$serverTagDir = sprintf('%s/sites-available/%s', $serverSettings['web_base_dir'], $serverTag);
		$serverTagEnabledDir = sprintf('%s/sites-enabled/%s', $serverSettings['web_base_dir'], $serverTag);
		$serverTagForHumansDir = sprintf('%s/sites-available-for-humans/%s.%s', $serverSettings['web_base_dir'], $serverPort, $serverName);
		$serverTagEnabledForHumansDir = sprintf('%s/sites-enabled-for-humans/%s.%s', $serverSettings['web_base_dir'], $serverPort, $serverName);
		
		// php-fpm
		$phpfpmDir = sprintf('/etc/php-fpm.d/settings/sites-available/%s', $serverTag);
		$phpfpmEnabledDir = sprintf('/etc/php-fpm.d/settings/sites-enabled/%s', $serverTag);
		$phpfpmForHumansDir = sprintf('/etc/php-fpm.d/settings/sites-available-for-humans/%s.%s', $serverPort, $serverName);
		$phpfpmEnabledForHumansDir = sprintf('/etc/php-fpm.d/settings/sites-enabled-for-humans/%s.%s', $serverPort, $serverName);
		
		// apache
		$apacheDir = sprintf('/etc/httpd/settings/sites-available/%s', $serverTag);
		$apacheEnabledDir = sprintf('/etc/httpd/settings/sites-enabled/%s', $serverTag);
		$apacheForHumansDir = sprintf('/etc/httpd/settings/sites-available-for-humans/%s.%s', $serverPort, $serverName);
		$apacheEnabledForHumansDir = sprintf('/etc/httpd/settings/sites-enabled-for-humans/%s.%s', $serverPort, $serverName);
		
		// nginx
		$nginxDir = sprintf('/etc/nginx/settings/sites-available/%s', $serverTag);
		$nginxEnabledDir = sprintf('/etc/nginx/settings/sites-enabled/%s', $serverTag);
		$nginxForHumansDir = sprintf('/etc/nginx/settings/sites-available-for-humans/%s.%s', $serverPort, $serverName);
		$nginxEnabledForHumansDir = sprintf('/etc/nginx/settings/sites-enabled-for-humans/%s.%s', $serverPort, $serverName);
		
		// webalizer
		$webalizerDir = sprintf('/etc/webalizer.d/settings/sites-available/%s', $serverTag);
		$webalizerEnabledDir = sprintf('/etc/webalizer.d/settings/sites-enabled/%s', $serverTag);
		$webalizerForHumansDir = sprintf('/etc/webalizer.d/settings/sites-available-for-humans/%s.%s', $serverPort, $serverName);
		$webalizerEnabledForHumansDir = sprintf('/etc/webalizer.d/settings/sites-enabled-for-humans/%s.%s', $serverPort, $serverName);
		
		// check if server_tag directory already exists
		exec(sprintf('sudo ls %s 2>&1', $serverTagDir), $output, $statusCode);
		if ($statusCode == 0) {
			self::$errorMessage .= implode("\r\n", $output)."\r\n";
			return false;
		}
		
		// creating session directory for this server_tag
		exec(sprintf('sudo mkdir -p /var/lib/php/session/%s 2>&1', $serverTag), $output, $statusCode);
		if ($statusCode != 0) {
			self::$errorMessage .= implode("\r\n", $output)."\r\n";
			return false;
		}
		
		// creating web root directory for this server_tag
		exec(sprintf('sudo mkdir -p %s/%s 2>&1', $serverTagDir, $serverSettings['web_root_dir']), $output, $statusCode);
		if ($statusCode != 0) {
			self::$errorMessage .= implode("\r\n", $output)."\r\n";
			return false;
		}
		
		// symlinks
		exec(sprintf('sudo ln -fs "../sites-available/%s/" "%s" 2>&1', $serverTag, $serverTagEnabledDir), $output, $statusCode); // sites-enabled
		exec(sprintf('sudo ln -fs "../sites-available/%s/" "%s" 2>&1', $serverTag, $serverTagForHumansDir), $output, $statusCode); // sites-available-for-humans
		exec(sprintf('sudo ln -fs "../sites-available/%s/" "%s" 2>&1', $serverTag, $serverTagEnabledForHumansDir), $output, $statusCode); // sites-enabled-for-humans
		
		// creating server_tag linux user
		self::addUser($serverTag, $serverTagDir, 'apache', sprintf('%s.%s', $serverPort, $serverName));
		
		// copying default index page
		$indexPageContent = preg_replace('#\{\{\s+server_tag\s+\}\}#i', $serverTag, $serverTemplates['index']);
		self::createFileFromContent(sprintf('%s/%s/index.php', $serverTagDir, $serverSettings['web_root_dir']), $indexPageContent);
		
		// creating php-fpm config
		exec(sprintf('sudo mv %s.conf %s.conf.bak 2>&1', $phpfpmDir, $phpfpmDir), $output, $statusCode);
		
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
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf" 2>&1', $serverTag, $phpfpmEnabledDir), $output, $statusCode); // sites-enabled
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf" 2>&1', $serverTag, $phpfpmForHumansDir), $output, $statusCode); // sites-available-for-humans
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf" 2>&1', $serverTag, $phpfpmEnabledForHumansDir), $output, $statusCode); // sites-enabled-for-humans
		
		
		// creating apache config
		exec(sprintf('sudo mv %s.conf %s.conf.bak 2>&1', $apacheDir, $apacheDir), $output, $statusCode);
		
		$apacheConfigContent = preg_replace('#\{\{\s+server_tag\s+\}\}#i', $serverTag, $serverTemplates['apache']);
		$apacheConfigContent = preg_replace('#\{\{\s+web_base_dir\s+\}\}#i', $serverSettings['web_base_dir'], $apacheConfigContent);
		$apacheConfigContent = preg_replace('#\{\{\s+web_root_dir\s+\}\}#i', $serverSettings['web_root_dir'], $apacheConfigContent);
		$apacheConfigContent = preg_replace('#\{\{\s+server_port\s+\}\}#i', $serverSettings['server_port'], $apacheConfigContent);
		$apacheConfigContent = preg_replace('#\{\{\s+server_name\s+\}\}#i', $serverSettings['server_name'], $apacheConfigContent);
		$apacheConfigContent = preg_replace('#\{\{\s+server_port_server_aliases\s+\}\}#i', $serverSettings['server_port_server_aliases'], $apacheConfigContent);
		$apacheConfigContent = preg_replace('#\{\{\s+mod_page_speed_domains\s+\}\}#i', $serverSettings['mod_page_speed_domains'], $apacheConfigContent);
		
		self::createFileFromContent(sprintf('%s.conf', $apacheDir), $apacheConfigContent);
		
		// symlinks
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf" 2>&1', $serverTag, $apacheEnabledDir), $output, $statusCode); // sites-enabled
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf" 2>&1', $serverTag, $apacheForHumansDir), $output, $statusCode); // sites-available-for-humans
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf" 2>&1', $serverTag, $apacheEnabledForHumansDir), $output, $statusCode); // sites-enabled-for-humans
		
		// creating nginx config
		exec(sprintf('sudo mv %s.conf %s.conf.bak 2>&1', $nginxDir, $nginxDir), $output, $statusCode);
		
		$nginxConfigContent = preg_replace('#\{\{\s+server_tag\s+\}\}#i', $serverTag, $serverTemplates['nginx']);
		$nginxConfigContent = preg_replace('#\{\{\s+web_base_dir\s+\}\}#i', $serverSettings['web_base_dir'], $nginxConfigContent);
		$nginxConfigContent = preg_replace('#\{\{\s+web_root_dir\s+\}\}#i', $serverSettings['web_root_dir'], $nginxConfigContent);
		$nginxConfigContent = preg_replace('#\{\{\s+server_port\s+\}\}#i', $serverSettings['server_port'], $nginxConfigContent);
		$nginxConfigContent = preg_replace('#\{\{\s+server_aliases\s+\}\}#i', $serverSettings['server_aliases'], $nginxConfigContent);
		$nginxConfigContent = preg_replace('#\{\{\s+limit_rate\s+\}\}#i', $serverSettings['limit_rate'], $nginxConfigContent);
		$nginxConfigContent = preg_replace('#\{\{\s+limit_conn\s+\}\}#i', $serverSettings['limit_conn'], $nginxConfigContent);
		
		self::createFileFromContent(sprintf('%s.conf', $nginxDir), $nginxConfigContent);
		
		// symlinks
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf" 2>&1', $serverTag, $nginxEnabledDir), $output, $statusCode); // sites-enabled
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf" 2>&1', $serverTag, $nginxForHumansDir), $output, $statusCode); // sites-available-for-humans
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf" 2>&1', $serverTag, $nginxEnabledForHumansDir), $output, $statusCode); // sites-enabled-for-humans
		
		// creating nginx default config
		exec('sudo mv /etc/nginx/nginx_default_server.conf /etc/nginx/nginx_default_server.conf.bak 2>&1', $output, $statusCode);
		$nginxDefaultConfigContent = preg_replace('#\{\{\s+default_server\s+\}\}#i', $serverSettings['default_server'], $serverTemplates['nginxdefault']);
		self::createFileFromContent('/etc/nginx/nginx_default_server.conf', $nginxDefaultConfigContent);
		
		// creating webalizer config
		exec(sprintf('sudo mv %s.conf %s.conf.bak 2>&1', $webalizerDir, $webalizerDir), $output, $statusCode);
		
		$webalizerConfigContent = preg_replace('#\{\{\s+server_tag\s+\}\}#i', $serverTag, $serverTemplates['webalizer']);
		
		self::createFileFromContent(sprintf('%s.conf', $webalizerDir), $webalizerConfigContent);
		
		// symlinks
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf" 2>&1', $serverTag, $webalizerEnabledDir), $output, $statusCode); // sites-enabled
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf" 2>&1', $serverTag, $webalizerForHumansDir), $output, $statusCode); // sites-available-for-humans
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" "%s.conf" 2>&1', $serverTag, $webalizerEnabledForHumansDir), $output, $statusCode); // sites-enabled-for-humans
		
		// adding server aliases to the hosts file
		// exec('sudo mv /etc/hosts /etc/hosts.bak 2>&1', $output, $statusCode);
		// exec('sudo cat /etc/hosts 2>&1', $output, $statusCode);
		// $hostsContent = preg_replace('!#\s*webpanel.*!is', '', implode("\r\n", $output));
		// self::createFileFromContent('/etc/hosts', $hostsContent.$serverSettings['hosts']);
		
		// setting permissions
		exec(sprintf('sudo chmod 777 /var/lib/php/session/%s 2>&1', $serverTag), $output, $statusCode);
		exec(sprintf('sudo chown -R "%s:apache" %s 2>&1', $serverTag, $serverTagDir), $output, $statusCode);
		exec(sprintf('sudo chmod -R 664 %s 2>&1', $serverTagDir), $output, $statusCode);
		exec(sprintf('sudo chmod -R +X %s 2>&1', $serverTagDir), $output, $statusCode);
		
		// reloading servers
		if ( ! self::reloadServers()) {
			return false;
		}
		
		return true;
	}
	
	public static function reloadServers()
	{
		exec('sudo service php-fpm start 2>&1', $output, $statusCode);
		exec('sudo service httpd start 2>&1', $output, $statusCode);
		exec('sudo service nginx start 2>&1', $output, $statusCode);
		exec('sudo service memcached start 2>&1', $output, $statusCode);
		
		if ( ! self::testServers()) {
			return false;
		}
		
		exec('sudo service php-fpm reload 2>&1', $output, $statusCode);
		if ($statusCode != 0) {
			self::$errorMessage .= implode("\r\n", $output)."\r\n";
			return false;
		}
		exec('sudo service httpd reload 2>&1', $output, $statusCode);
		if ($statusCode != 0) {
			self::$errorMessage .= implode("\r\n", $output)."\r\n";
			return false;
		}
		exec('sudo service nginx reload 2>&1', $output, $statusCode);
		if ($statusCode != 0) {
			self::$errorMessage .= implode("\r\n", $output)."\r\n";
			return false;
		}
		
		return true;
	}
	
	public static function testServers()
	{
		// testing php-fpm
		exec('sudo /usr/sbin/php-fpm -t 2>&1', $output, $statusCode);
		if ($statusCode != 0) {
			self::$errorMessage .= implode("\r\n", $output)."\r\n";
			return false;
		}
		
		// testing apache
		exec('sudo /usr/sbin/httpd -t 2>&1', $output, $statusCode);
		if ($statusCode != 0) {
			self::$errorMessage .= implode("\r\n", $output)."\r\n";
			return false;
		}
		
		// testing nginx
		exec('sudo /usr/sbin/nginx -t 2>&1', $output, $statusCode);
		if ($statusCode != 0) {
			self::$errorMessage .= implode("\r\n", $output)."\r\n";
			return false;
		}
		
		return true;
	}
	
	public static function createFileFromContent($path, $content, $append = false)
	{
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
	
	public static function addUser($username, $home = '', $group = '', $comment = '', $shell = '/sbin/nologin', $uid = null, $password = '')
	{
		$command = '';
		if ($comment) { $command .= sprintf(' --comment "%s" ', $comment); }
		if ($group) { $command .= sprintf(' -g %s ', $group); }
		if ($home) { $command .= sprintf(' --home "%s" ', $home); }
		if ($shell) { $command .= sprintf(' --shell "%s" ', $shell); }
		if ($uid) { $command .= sprintf(' -u %s -o ', $shell); }
		$command .= sprintf(' "%s"', $username);
	
		// check if username exists
		exec(sprintf('sudo id %s 2>&1', $username), $output, $statusCode);
		if ($statusCode == 0) {
			// user exists
			exec(sprintf('sudo usermod %s 2>&1', $command), $output, $statusCode);
		} else {
			// user doesn't exist
			exec(sprintf('sudo useradd %s 2>&1', $command), $output, $statusCode);
		}
		
		if ($password) {
			exec(sprintf('echo "%s":"%s" | sudo chpasswd 2>&1', $username, $password), $output, $statusCode);
		}
		
		return true;
		
	}
	
	public static function removeSite($serverTag, $serverName, $serverPort)
	{
		// removing php-fpm config files
		exec(sprintf('sudo rm -f /etc/php-fpm.d/settings/sites-available/%s.conf 2>&1', $serverTag), $output, $statusCode);
		exec(sprintf('sudo rm -f /etc/php-fpm.d/settings/sites-enabled/%s.conf 2>&1', $serverTag), $output, $statusCode);
		exec(sprintf('sudo rm -f /etc/php-fpm.d/settings/sites-available-for-humans/%s.%s.conf 2>&1', $serverPort, $serverName), $output, $statusCode);
		exec(sprintf('sudo rm -f /etc/php-fpm.d/settings/sites-enabled-for-humans/%s.%s.conf 2>&1', $serverPort, $serverName), $output, $statusCode);
		
		// removing apache config files
		exec(sprintf('sudo rm -f /etc/httpd/settings/sites-available/%s.conf 2>&1', $serverTag), $output, $statusCode);
		exec(sprintf('sudo rm -f /etc/httpd/settings/sites-enabled/%s.conf 2>&1', $serverTag), $output, $statusCode);
		exec(sprintf('sudo rm -f /etc/httpd/settings/sites-available-for-humans/%s.%s.conf 2>&1', $serverPort, $serverName), $output, $statusCode);
		exec(sprintf('sudo rm -f /etc/httpd/settings/sites-enabled-for-humans/%s.%s.conf 2>&1', $serverPort, $serverName), $output, $statusCode);
		
		// removing nginx config files
		exec(sprintf('sudo rm -f /etc/nginx/settings/sites-available/%s.conf 2>&1', $serverTag), $output, $statusCode);
		exec(sprintf('sudo rm -f /etc/nginx/settings/sites-enabled/%s.conf 2>&1', $serverTag), $output, $statusCode);
		exec(sprintf('sudo rm -f /etc/nginx/settings/sites-available-for-humans/%s.%s.conf 2>&1', $serverPort, $serverName), $output, $statusCode);
		exec(sprintf('sudo rm -f /etc/nginx/settings/sites-enabled-for-humans/%s.%s.conf 2>&1', $serverPort, $serverName), $output, $statusCode);
		
		// removing webalizer config files
		exec(sprintf('sudo rm -f /etc/webalizer.d/settings/sites-available/%s.conf 2>&1', $serverTag), $output, $statusCode);
		exec(sprintf('sudo rm -f /etc/webalizer.d/settings/sites-enabled/%s.conf 2>&1', $serverTag), $output, $statusCode);
		exec(sprintf('sudo rm -f /etc/webalizer.d/settings/sites-available-for-humans/%s.%s.conf 2>&1', $serverPort, $serverName), $output, $statusCode);
		exec(sprintf('sudo rm -f /etc/webalizer.d/settings/sites-enabled-for-humans/%s.%s.conf 2>&1', $serverPort, $serverName), $output, $statusCode);
		
		// web
		exec(sprintf('sudo rm -f %s/sites-enabled/%s 2>&1', Config::get('panel.web_base_dir'), $serverTag), $output, $statusCode);
		exec(sprintf('sudo rm -f %s/sites-enabled-for-humans/%s.%s 2>&1', Config::get('panel.web_base_dir'), $serverPort, $serverName), $output, $statusCode);
		
		
		// /etc/hosts
		
		// reloading servers
		if ( ! self::reloadServers()) {
			return false;
		}
		
		// removing server_tag linux user
		exec(sprintf('sudo userdel %s 2>&1', $serverTag), $output, $statusCode);
		
		// setting owner to nobody
		exec(sprintf('sudo chown -R "nobody:apache" %s/sites-available/%s 2>&1', Config::get('panel.web_base_dir'), $serverTag), $output, $statusCode);
		
		return true;
	}
	
	public static function enableSite($serverTag, $serverName, $serverPort)
	{
		// symlinks
		// php-fpm
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" /etc/php-fpm.d/settings/sites-enabled/%s.conf 2>&1', $serverTag, $serverTag), $output, $statusCode);
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" /etc/php-fpm.d/settings/sites-enabled-for-humans/%s.%s.conf 2>&1', $serverTag, $serverPort, $serverName), $output, $statusCode);
		
		// apache
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" /etc/httpd/settings/sites-enabled/%s.conf 2>&1', $serverTag, $serverTag), $output, $statusCode);
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" /etc/httpd/settings/sites-enabled-for-humans/%s.%s.conf 2>&1', $serverTag, $serverPort, $serverName), $output, $statusCode);
		
		// nginx
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" /etc/nginx/settings/sites-enabled/%s.conf 2>&1', $serverTag, $serverTag), $output, $statusCode);
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" /etc/nginx/settings/sites-enabled-for-humans/%s.%s.conf 2>&1', $serverTag, $serverPort, $serverName), $output, $statusCode);
		
		// webalizer
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" /etc/webalizer.d/settings/sites-enabled/%s.conf 2>&1', $serverTag, $serverTag), $output, $statusCode);
		exec(sprintf('sudo ln -fs "../sites-available/%s.conf" /etc/webalizer.d/settings/sites-enabled-for-humans/%s.%s.conf 2>&1', $serverTag, $serverPort, $serverName), $output, $statusCode);
		
		// web
		exec(sprintf('sudo ln -fs "../sites-available/%s" %s/sites-enabled/%s 2>&1', $serverTag, Config::get('panel.web_base_dir'), $serverTag), $output, $statusCode);
		exec(sprintf('sudo ln -fs "../sites-available/%s" %s/sites-enabled-for-humans/%s.%s 2>&1', $serverTag, Config::get('panel.web_base_dir'), $serverPort, $serverName), $output, $statusCode);
		
		// reloading servers
		if ( ! self::reloadServers()) {
			return false;
		}
		
		return true;
	}
	
	public static function disableSite($serverTag, $serverName, $serverPort)
	{
		// removing symlinks
		// php-fpm
		exec(sprintf('sudo rm -f /etc/php-fpm.d/settings/sites-enabled/%s.conf 2>&1', $serverTag), $output, $statusCode);
		exec(sprintf('sudo rm -f /etc/php-fpm.d/settings/sites-enabled-for-humans/%s.%s.conf 2>&1', $serverPort, $serverName), $output, $statusCode);
		
		// apache
		exec(sprintf('sudo rm -f /etc/httpd/settings/sites-enabled/%s.conf 2>&1', $serverTag), $output, $statusCode);
		exec(sprintf('sudo rm -f /etc/httpd/settings/sites-enabled-for-humans/%s.%s.conf 2>&1', $serverPort, $serverName), $output, $statusCode);
		
		// nginx
		exec(sprintf('sudo rm -f /etc/nginx/settings/sites-enabled/%s.conf 2>&1', $serverTag), $output, $statusCode);
		exec(sprintf('sudo rm -f /etc/nginx/settings/sites-enabled-for-humans/%s.%s.conf 2>&1', $serverPort, $serverName), $output, $statusCode);
		
		// webalizer
		exec(sprintf('sudo rm -f /etc/webalizer.d/settings/sites-enabled/%s.conf 2>&1', $serverTag), $output, $statusCode);
		exec(sprintf('sudo rm -f /etc/webalizer.d/settings/sites-enabled-for-humans/%s.%s.conf 2>&1', $serverPort, $serverName), $output, $statusCode);
		
		// web
		exec(sprintf('sudo rm -f %s/sites-enabled/%s 2>&1', Config::get('panel.web_base_dir'), $serverTag), $output, $statusCode);
		exec(sprintf('sudo rm -f %s/sites-enabled-for-humans/%s.%s 2>&1', Config::get('panel.web_base_dir'), $serverPort, $serverName), $output, $statusCode);
		
		// reloading servers
		if ( ! self::reloadServers()) {
			return false;
		}
		
		return true;
	}
	
	public static function enableUser ($username) {
	
		exec(sprintf('sudo usermod --unlock --expiredate 2025-01-01 %s 2>&1', $username), $output, $statusCode);
		
		// 0: root
		exec(sprintf('sudo sed -i -r -e"s/^(%s:[^:]+):[0-9]+:/\1:0:/" "/etc/passwd" 2>&1', $username), $output, $statusCode);		

		return true;

	}
	
	public static function disableUser ($username) {
		
		// 99: nobody
		exec(sprintf('sudo sed -i -r -e"s/^(%s:[^:]+):[0-9]+:/\1:99:/" "/etc/passwd" 2>&1', $username), $output, $statusCode);
		
		exec(sprintf('sudo usermod --lock --expiredate 1970-01-01 %s 2>&1', $username), $output, $statusCode);
		
		if ($statusCode == 0) {
			return true;
		} else {
			return false;
		}

	}
	
	public static function removeUser ($username) {
	
		// 99: nobody
		exec(sprintf('sudo sed -i -r -e"s/^(%s:[^:]+):[0-9]+:/\1:99:/" "/etc/passwd" 2>&1', $username), $output, $statusCode);
		
		exec(sprintf('sudo userdel %s 2>&1', $username), $output, $statusCode);
		
		if ($statusCode == 0) {
			return true;
		} else {
			return false;
		}
	}
	
	// public static function addUser ($username, $password) {
		// $panelCommandsPath = Config::get('panel.panel_commands_path');
		
		// exec("sudo $panelCommandsPath/userdef.sh $username $password", $output, $statusCode);
		
		// if ($statusCode == 0) {
			// return true;
		// } else {
			// return false;
		// }
	// }
	
}