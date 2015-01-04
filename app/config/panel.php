<?php

$webBaseDir = '/var/www/WebPanel';
$webRootDir = 'web';

return array(
	'web_base_dir' => $webBaseDir,
	'web_root_dir' => $webRootDir,
	'panel_commands_path' => '/opt/webpanel/cmd',
	
	'server_settings' => array(
		'activated' => '1',
		'server_tag' => '',
		'server_port' => '80',
		'server_name' => '', // default domain
		'server_quota' => '10',
		'default_server' => 'listen 80 default_server;',
		'hosts' => '# webpanel', // /etc/hosts
		'server_aliases' => '', // nginx -> all domains
		'server_port_server_aliases' => '', // apache -> all domains with port
		'mod_page_speed_domains' => '', // line separated mod_page_speed settings for each domain
		
		'web_base_dir' => $webBaseDir,
		'web_root_dir' => $webRootDir,
		'limit_rate' => '25',
		'limit_conn' => '100',
		'max_children' => '2',
		'start_servers' => '1',
		'min_spare_servers' => '1',
		'max_spare_servers' => '1',
		'max_requests' => '5000',
		'request_terminate_timeout' => '60',
		'request_slowlog_timeout' => '5',
		'post_max_size' => '8',
		'upload_max_filesize' => '2',
		'max_file_uploads' => '20',
		'memory_limit' => '32',
		'max_execution_time' => '30',
		'error_reporting' => 'E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT',
		'max_input_time' => '30',
		'default_socket_timeout' => '30',
		'date_timezone' => 'Asia/Tehran',
		'output_buffering' => '4096',
	
	),

);
