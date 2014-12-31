<?php

class SiteTemplatesTableSeeder extends Seeder {

	public function run()
	{
		$nginxConfig = <<<'NGINX'
server {

		listen {{ server_port }};

		server_name {{ server_aliases }};

		root {{ web_base_dir }}/sites-available/{{ server_tag }}/{{ web_root_dir }};

		access_log /var/log/nginx/{{ server_tag }}_access.log timed_combined buffer=16k;
		error_log /var/log/nginx/{{ server_tag }}_error.log error;
		log_not_found off;
		log_subrequest off;

		## Deny not compatible request methods without 405 response.
		if ($request_method !~ ^(?:GET|HEAD|POST|OPTIONS)$) {
			return 403;
		}

		## Deny crawlers.
		if ($is_crawler) {
			return 403;
		}

		## Network Limits
		limit_req                          zone=limit_req_perip burst=100;
		limit_rate                                                    {{ limit_rate }}k;
		limit_conn                                limit_conn_pervhost {{ limit_conn }};

		error_page 502 = @badgateway;
		location @badgateway {
			return 444;
		}
		
		error_page 504 = @gatewaytimeout;
		location @gatewaytimeout {
			return 444;
		}

		error_page 429 = @toomany;
		location @toomany {
			return 403;
		}

		# Block access to "hidden" files and directories whose names begin with a
		# period. This includes directories used by version control systems such
		# as Subversion or Git to store control files.
		location ~ (?:^|/)\. {
			return 403;
		}

		location = /favicon.ico {
			try_files $uri =404;
			access_log off;
		}

		location = /robots.txt {
			try_files $uri =404;
			access_log off;
		}

		# Very rarely should these ever be accessed outside of your lan
		location ~* \.(?:txt|log)$ {
			allow 192.168.0.0/16;
			allow 172.16.0.0/12;
			allow 10.0.0.0/8;
			deny all;
		}

		location ~ \..*/.*\.php$ {
			return 403;
		}

		# Static files will be sent directly by Nginx
		location ~* \.(?:htm|html|xml|css|js|ico|png|jpg|jpeg|gif|bmp|tif|tiff|svg|swf|flv|mp3|ogg|mid|midi|wav|m4a|wma|3gp|mp4|m4v|mpeg|mpg|mov|mkv|dat|webm|avi|asx|asf|wmv|otf|ttf|woff|eot|doc|docx|pdf|rtf|xls|xlsx|ppt|pptx|jar|7z|rar|zip|tar|tgz|bz2|gz|bz|bin|exe|dll|msi|msp|nrg|iso|img|mdf|chm|djvu|dmg|flac)$ {

			try_files $uri @backend;

			add_header  Access-Control-Allow-Origin *;
			expires            7d;
			sendfile          off;
			tcp_nodelay       off;
			tcp_nopush        off;
			output_buffers 1 512k;
			aio                on;
			directio          512;
		}

		location ~* \.php$ {
			error_page 403 = @backend;
			return 403;
		}

		location / {

			try_files $uri @backend;
		}

		location @backend {

			proxy_pass      http://http_backend;
		}
}
NGINX;

		$apacheConfig = <<<'APACHE'
<VirtualHost *:*>
	DocumentRoot "{{ web_base_dir }}/sites-available/{{ server_tag }}/{{ web_root_dir }}"

	<Directory "{{ web_base_dir }}/sites-available/{{ server_tag }}/{{ web_root_dir }}">
		Order allow,deny
		Allow from all

		AllowOverride All
	</Directory>

	<Location />
		Options -Indexes -Includes -FollowSymLinks -ExecCGI +IncludesNoExec +SymLinksIfOwnerMatch
	</Location>

	#ServerName {{ server_port }}.{{ server_name }}
	ServerAlias {{ server_port_server_aliases }}

	#ModPagespeedDomain *domain.tld
	{{ mod_page_speed_domains }}

	ServerAdmin postmaster@{{ server_name }}

	ErrorLog /var/log/httpd/{{ server_tag }}_error.log
	#CustomLog /var/log/httpd/{{ server_tag }}_access.log combined

	<IfModule mod_fastcgi.c>
		<IfModule mod_actions.c>
			<IfModule mod_alias.c>
				DirectoryIndex index.php index.html index.htm
				AddHandler php5-fcgi-{{ server_tag }} .php
				Action php5-fcgi-{{ server_tag }} /php5-fcgi-{{ server_tag }}
				Alias /php5-fcgi-{{ server_tag }} /usr/lib/cgi-bin/php5-fcgi-{{ server_tag }}
				FastCgiExternalServer /usr/lib/cgi-bin/php5-fcgi-{{ server_tag }} -socket /var/run/php-fpm/{{ server_tag }}.sock -pass-header Authorization -flush -idle-timeout 3600
			</IfModule>
		</IfModule>
	</IfModule>
</VirtualHost>
APACHE;

		$phpfpmConfig = <<<'PHPFPM'
; Start a new pool named '{{ server_tag }}'.
[{{ server_tag }}]
; The address on which to accept FastCGI requests.
; Valid syntaxes are:
;   'ip.add.re.ss:port'    - to listen on a TCP socket to a specific address on
;                            a specific port;
;   'port'                 - to listen on a TCP socket to all addresses on a
;                            specific port;
;   '/path/to/unix/socket' - to listen on a unix socket.
; Note: This value is mandatory.
;listen = 127.0.0.1:9000
listen = /var/run/php-fpm/{{ server_tag }}.sock

; Set listen(2) backlog. A value of '-1' means unlimited.
; Default Value: -1
;listen.backlog = -1

; List of ipv4 addresses of FastCGI clients which are allowed to connect.
; Equivalent to the FCGI_WEB_SERVER_ADDRS environment variable in the original
; PHP FCGI (5.2.2+). Makes sense only with a tcp listening socket. Each address
; must be separated by a comma. If this value is left blank, connections will be
; accepted from any ip address.
; Default Value: any
listen.allowed_clients = 127.0.0.1

; Set permissions for unix socket, if one is used. In Linux, read/write
; permissions must be set in order to allow connections from a web server. Many
; BSD-derived systems allow connections regardless of permissions.
; Default Values: user and group are set as the running user
;                 mode is set to 0660
;listen.owner = nobody
;listen.group = nobody
;listen.mode = 0660
listen.mode = 0666

; Unix user/group of processes
; Note: The user is mandatory. If the group is not set, the default user's group
;       will be used.
; RPM: apache Choosed to be able to access some dir as httpd
;user = apache
user = {{ server_tag }}
; RPM: Keep a group allowed to write in log dir.
group = apache

; Choose how the process manager will control the number of child processes.
; Possible Values:
;   static  - a fixed number (pm.max_children) of child processes;
;   dynamic - the number of child processes are set dynamically based on the
;             following directives:
;             pm.max_children      - the maximum number of children that can
;                                    be alive at the same time.
;             pm.start_servers     - the number of children created on startup.
;             pm.min_spare_servers - the minimum number of children in 'idle'
;                                    state (waiting to process). If the number
;                                    of 'idle' processes is less than this
;                                    number then some children will be created.
;             pm.max_spare_servers - the maximum number of children in 'idle'
;                                    state (waiting to process). If the number
;                                    of 'idle' processes is greater than this
;                                    number then some children will be killed.
; Note: This value is mandatory.
pm = dynamic

; The number of child processes to be created when pm is set to 'static' and the
; maximum number of child processes to be created when pm is set to 'dynamic'.
; This value sets the limit on the number of simultaneous requests that will be
; served. Equivalent to the ApacheMaxClients directive with mpm_prefork.
; Equivalent to the PHP_FCGI_CHILDREN environment variable in the original PHP
; CGI.
; Note: Used when pm is set to either 'static' or 'dynamic'
; Note: This value is mandatory.
pm.max_children = {{ max_children }}

; The number of child processes created on startup.
; Note: Used only when pm is set to 'dynamic'
; Default Value: min_spare_servers + (max_spare_servers - min_spare_servers) / 2
pm.start_servers = {{ start_servers }}

; The desired minimum number of idle server processes.
; Note: Used only when pm is set to 'dynamic'
; Note: Mandatory when pm is set to 'dynamic'
pm.min_spare_servers = {{ min_spare_servers }}

; The desired maximum number of idle server processes.
; Note: Used only when pm is set to 'dynamic'
; Note: Mandatory when pm is set to 'dynamic'
pm.max_spare_servers = {{ max_spare_servers }}

; The number of requests each child process should execute before respawning.
; This can be useful to work around memory leaks in 3rd party libraries. For
; endless request processing specify '0'. Equivalent to PHP_FCGI_MAX_REQUESTS.
; Default Value: 0
pm.max_requests = {{ max_requests }}

; The URI to view the FPM status page. If this value is not set, no URI will be
; recognized as a status page. By default, the status page shows the following
; information:
;   accepted conn    - the number of request accepted by the pool;
;   pool             - the name of the pool;
;   process manager  - static or dynamic;
;   idle processes   - the number of idle processes;
;   active processes - the number of active processes;
;   total processes  - the number of idle + active processes.
; The values of 'idle processes', 'active processes' and 'total processes' are
; updated each second. The value of 'accepted conn' is updated in real time.
; Example output:
;   accepted conn:   12073
;   pool:             www
;   process manager:  static
;   idle processes:   35
;   active processes: 65
;   total processes:  100
; By default the status page output is formatted as text/plain. Passing either
; 'html' or 'json' as a query string will return the corresponding output
; syntax. Example:
;   http://www.foo.bar/status
;   http://www.foo.bar/status?json
;   http://www.foo.bar/status?html
; Note: The value must start with a leading slash (/). The value can be
;       anything, but it may not be a good idea to use the .php extension or it
;       may conflict with a real PHP file.
; Default Value: not set
pm.status_path = /status

; The ping URI to call the monitoring page of FPM. If this value is not set, no
; URI will be recognized as a ping page. This could be used to test from outside
; that FPM is alive and responding, or to
; - create a graph of FPM availability (rrd or such);
; - remove a server from a group if it is not responding (load balancing);
; - trigger alerts for the operating team (24/7).
; Note: The value must start with a leading slash (/). The value can be
;       anything, but it may not be a good idea to use the .php extension or it
;       may conflict with a real PHP file.
; Default Value: not set
ping.path = /ping

; This directive may be used to customize the response of a ping request. The
; response is formatted as text/plain with a 200 response code.
; Default Value: pong
ping.response = pong

; The timeout for serving a single request after which the worker process will
; be killed. This option should be used when the 'max_execution_time' ini option
; does not stop script execution for some reason. A value of '0' means 'off'.
; Available units: s(econds)(default), m(inutes), h(ours), or d(ays)
; Default Value: 0
request_terminate_timeout = {{ request_terminate_timeout }}

access.format = "%R - %u [%t] \"%m %r%Q%q\" %s %f %{mili}d %{kilo}M %C%%"
access.log = /var/log/php-fpm/$pool_access.log

; The timeout for serving a single request after which a PHP backtrace will be
; dumped to the 'slowlog' file. A value of '0s' means 'off'.
; Available units: s(econds)(default), m(inutes), h(ours), or d(ays)
; Default Value: 0
request_slowlog_timeout = {{ request_slowlog_timeout }}

; The log file for slow requests
; Default Value: not set
; Note: slowlog is mandatory if request_slowlog_timeout is set
slowlog = /var/log/php-fpm/$pool_slow.log

; Set open file descriptor rlimit.
; Default Value: system defined value
;rlimit_files = 1024

; Set max core size rlimit.
; Possible Values: 'unlimited' or an integer greater or equal to 0
; Default Value: system defined value
;rlimit_core = 0

; Chroot to this directory at the start. This value must be defined as an
; absolute path. When this value is not set, chroot is not used.
; Note: chrooting is a great security feature and should be used whenever
;       possible. However, all PHP paths will be relative to the chroot
;       (error_log, sessions.save_path, ...).
; Default Value: not set
;chroot =

; Chdir to this directory at the start. This value must be an absolute path.
; Default Value: current directory or / when chroot
;chdir = /var/www

; Redirect worker stdout and stderr into main error log. If not set, stdout and
; stderr will be redirected to /dev/null according to FastCGI specs.
; Default Value: no
catch_workers_output = yes

; Limits the extensions of the main script FPM will allow to parse. This can
; prevent configuration mistakes on the web server side. You should only limit
; FPM to .php extensions to prevent malicious users to use other extensions to
; exectute php code.
; Note: set an empty value to allow all extensions.
; Default Value: .php
;security.limit_extensions = .php .php3 .php4 .php5
; Pass environment variables like LD_LIBRARY_PATH. All $VARIABLEs are taken from
; the current environment.
; Default Value: clean env
;env[HOSTNAME] = $HOSTNAME
;env[PATH] = /usr/local/bin:/usr/bin:/bin
;env[TMP] = /tmp
;env[TMPDIR] = /tmp
;env[TEMP] = /tmp

; Additional php.ini defines, specific to this pool of workers. These settings
; overwrite the values previously defined in the php.ini. The directives are the
; same as the PHP SAPI:
;   php_value/php_flag             - you can set classic ini defines which can
;                                    be overwritten from PHP call 'ini_set'.
;   php_admin_value/php_admin_flag - these directives won't be overwritten by
;                                     PHP call 'ini_set'
; For php_*flag, valid values are on, off, 1, 0, true, false, yes or no.

; Defining 'extension' will load the corresponding shared extension from
; extension_dir. Defining 'disable_functions' or 'disable_classes' will not
; overwrite previously defined php.ini values, but will append the new value
; instead.

; Default Value: nothing is defined by default except the values in php.ini and
;                specified at startup with the -d argument

; can't be changed by panel admins
php_admin_flag[display_errors] = off
php_admin_flag[display_startup_errors] = off
php_admin_flag[log_errors] = on
php_admin_flag[zlib.output_compression] = off
php_admin_flag[implicit_flush] = off
php_admin_flag[expose_php] = off
php_admin_flag[allow_url_include] = off
php_admin_flag[ignore_repeated_errors] = on
php_admin_flag[ignore_repeated_source] = off
php_admin_flag[html_errors] = off
php_admin_flag[enable_dl] = off
php_admin_flag[ignore_user_abort] = on
php_admin_value[variables_order] = "GPCS"
php_admin_value[request_order] = "GP"
php_admin_value[max_input_nesting_level] = 64
php_admin_value[max_input_vars] = 1000
php_admin_value[log_errors_max_len] = 4096
php_admin_value[error_log] = /var/log/php-fpm/$pool_error.log
php_admin_value[open_basedir] = "/usr/share/pear:/tmp:{{ web_base_dir }}/sites-available/{{ server_tag }}"

php_value[session.save_path] = /var/lib/php/session/{{ server_tag }}
php_value[session.save_handler] = files

;;;;;;;;;;;;;;;;

; can be changed by panel admins
php_admin_value[post_max_size] = {{ post_max_size }}M
php_admin_value[upload_max_filesize] = {{ upload_max_filesize }}M
php_admin_value[max_file_uploads] = {{ max_file_uploads }}
php_admin_value[memory_limit] = {{ memory_limit }}M
php_admin_value[max_execution_time] = {{ max_execution_time }}
php_admin_value[error_reporting] = {{ error_reporting }}
php_admin_value[max_input_time] = {{ max_input_time }}
php_admin_value[default_socket_timeout] = {{ default_socket_timeout }}

php_value[date.timezone] = {{ date_timezone }}
php_value[output_buffering] = {{ output_buffering }}

PHPFPM;

		$webalizerConfig = <<<'WEBALIZER'
#
# Sample Webalizer configuration file
# Copyright 1997-2000 by Bradford L. Barrett (brad@mrunix.net)
#
# Distributed under the GNU General Public License.  See the
# files "Copyright" and "COPYING" provided with the webalizer
# distribution for additional information.
#
# This is a sample configuration file for the Webalizer (ver 2.01)
# Lines starting with pound signs '#' are comment lines and are
# ignored.  Blank lines are skipped as well.  Other lines are considered
# as configuration lines, and have the form "ConfigOption  Value" where
# ConfigOption is a valid configuration keyword, and Value is the value
# to assign that configuration option.  Invalid keyword/values are
# ignored, with appropriate warnings being displayed.  There must be
# at least one space or tab between the keyword and its value.
#
# As of version 0.98, The Webalizer will look for a 'default' configuration
# file named "webalizer.conf" in the current directory, and if not found
# there, will look for "/etc/webalizer.conf".


# LogFile defines the web server log file to use.  If not specified
# here or on on the command line, input will default to STDIN.  If
# the log filename ends in '.gz' (ie: a gzip compressed file), it will
# be decompressed on the fly as it is being read.

LogFile        /var/log/nginx/{{ server_tag }}_access.log

# LogType defines the log type being processed.  Normally, the Webalizer
# expects a CLF or Combined web server log as input.  Using this option,
# you can process ftp logs as well (xferlog as produced by wu-ftp and
# others), or Squid native logs.  Values can be 'clf', 'ftp' or 'squid',
# with 'clf' the default.

#LogType	clf

# OutputDir is where you want to put the output files.  This should
# should be a full path name, however relative ones might work as well.
# If no output directory is specified, the current directory will be used.

OutputDir      /opt/webpanel/wpdata/stats/{{ server_tag }}

# HistoryName allows you to specify the name of the history file produced
# by the Webalizer.  The history file keeps the data for up to 12 months
# worth of logs, used for generating the main HTML page (index.html).
# The default is a file named "webalizer.hist", stored in the specified
# output directory.  If you specify just the filename (without a path),
# it will be kept in the specified output directory.  Otherwise, the path
# is relative to the output directory, unless absolute (leading /).

HistoryName	/var/lib/webalizer/{{ server_tag }}.hist

# Incremental processing allows multiple partial log files to be used
# instead of one huge one.  Useful for large sites that have to rotate
# their log files more than once a month.  The Webalizer will save its
# internal state before exiting, and restore it the next time run, in
# order to continue processing where it left off.  This mode also causes
# The Webalizer to scan for and ignore duplicate records (records already
# processed by a previous run).  See the README file for additional
# information.  The value may be 'yes' or 'no', with a default of 'no'.
# The file 'webalizer.current' is used to store the current state data,
# and is located in the output directory of the program (unless changed
# with the IncrementalName option below).  Please read at least the section
# on Incremental processing in the README file before you enable this option.

Incremental	yes

# IncrementalName allows you to specify the filename for saving the
# incremental data in.  It is similar to the HistoryName option where the
# name is relative to the specified output directory, unless an absolute
# filename is specified.  The default is a file named "webalizer.current"
# kept in the normal output directory.  If you don't specify "Incremental"
# as 'yes' then this option has no meaning.

IncrementalName	/var/lib/webalizer/{{ server_tag }}.current

# ReportTitle is the text to display as the title.  The hostname
# (unless blank) is appended to the end of this string (seperated with
# a space) to generate the final full title string.
# Default is (for english) "Usage Statistics for".

#ReportTitle    Usage Statistics for

# HostName defines the hostname for the report.  This is used in
# the title, and is prepended to the URL table items.  This allows
# clicking on URL's in the report to go to the proper location in
# the event you are running the report on a 'virtual' web server,
# or for a server different than the one the report resides on.
# If not specified here, or on the command line, webalizer will
# try to get the hostname via a uname system call.  If that fails,
# it will default to "localhost".

HostName       {{ server_tag }}

# HTMLExtension allows you to specify the filename extension to use
# for generated HTML pages.  Normally, this defaults to "html", but
# can be changed for sites who need it (like for PHP embeded pages).

#HTMLExtension  html

# PageType lets you tell the Webalizer what types of URL's you
# consider a 'page'.  Most people consider html and cgi documents
# as pages, while not images and audio files.  If no types are
# specified, defaults will be used ('htm*', 'cgi' and HTMLExtension
# if different for web logs, 'txt' for ftp logs).

PageType	htm*
PageType	cgi
PageType        php
PageType        shtml
#PageType	phtml
#PageType	php3
#PageType	pl

# UseHTTPS should be used if the analysis is being run on a
# secure server, and links to urls should use 'https://' instead
# of the default 'http://'.  If you need this, set it to 'yes'.
# Default is 'no'.  This only changes the behaviour of the 'Top
# URL's' table.

#UseHTTPS       no

# DNSCache specifies the DNS cache filename to use for reverse DNS lookups.
# This file must be specified if you wish to perform name lookups on any IP
# addresses found in the log file.  If an absolute path is not given as
# part of the filename (ie: starts with a leading '/'), then the name is
# relative to the default output directory.  See the DNS.README file for
# additional information.

DNSCache	/var/lib/webalizer/dns_cache.db

# DNSChildren allows you to specify how many "children" processes are
# run to perform DNS lookups to create or update the DNS cache file.
# If a number is specified, the DNS cache file will be created/updated
# each time the Webalizer is run, immediately prior to normal processing,
# by running the specified number of "children" processes to perform
# DNS lookups.  If used, the DNS cache filename MUST be specified as
# well.  The default value is zero (0), which disables DNS cache file
# creation/updates at run time.  The number of children processes to
# run may be anywhere from 1 to 100, however a large number may effect
# normal system operations.  Reasonable values should be between 5 and
# 20.  See the DNS.README file for additional information.

DNSChildren	10

# HTMLPre defines HTML code to insert at the very beginning of the
# file.  Default is the DOCTYPE line shown below.  Max line length
# is 80 characters, so use multiple HTMLPre lines if you need more.

#HTMLPre <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

# HTMLHead defines HTML code to insert within the <HEAD></HEAD>
# block, immediately after the <TITLE> line.  Maximum line length
# is 80 characters, so use multiple lines if needed.

#HTMLHead <META NAME="author" CONTENT="The Webalizer">

# HTMLBody defined the HTML code to be inserted, starting with the
# <BODY> tag.  If not specified, the default is shown below.  If
# used, you MUST include your own <BODY> tag as the first line.
# Maximum line length is 80 char, use multiple lines if needed.

#HTMLBody <BODY BGCOLOR="#E8E8E8" TEXT="#000000" LINK="#0000FF" VLINK="#FF0000">

# HTMLPost defines the HTML code to insert immediately before the
# first <HR> on the document, which is just after the title and
# "summary period"-"Generated on:" lines.  If anything, this should
# be used to clean up in case an image was inserted with HTMLBody.
# As with HTMLHead, you can define as many of these as you want and
# they will be inserted in the output stream in order of apperance.
# Max string size is 80 characters.  Use multiple lines if you need to.

#HTMLPost 	<BR CLEAR="all">

# HTMLTail defines the HTML code to insert at the bottom of each
# HTML document, usually to include a link back to your home
# page or insert a small graphic.  It is inserted as a table
# data element (ie: <TD> your code here </TD>) and is right
# alligned with the page.  Max string size is 80 characters.

#HTMLTail <IMG SRC="msfree.png" ALT="100% Micro$oft free!">

# HTMLEnd defines the HTML code to add at the very end of the
# generated files.  It defaults to what is shown below.  If
# used, you MUST specify the </BODY> and </HTML> closing tags
# as the last lines.  Max string length is 80 characters.

#HTMLEnd </BODY></HTML>

# The Quiet option suppresses output messages... Useful when run
# as a cron job to prevent bogus e-mails.  Values can be either
# "yes" or "no".  Default is "no".  Note: this does not suppress
# warnings and errors (which are printed to stderr).

Quiet		yes

# ReallyQuiet will supress all messages including errors and
# warnings.  Values can be 'yes' or 'no' with 'no' being the
# default.  If 'yes' is used here, it cannot be overriden from
# the command line, so use with caution.  A value of 'no' has
# no effect.

#ReallyQuiet	no

# TimeMe allows you to force the display of timing information
# at the end of processing.  A value of 'yes' will force the
# timing information to be displayed.  A value of 'no' has no
# effect.

#TimeMe		no

# GMTTime allows reports to show GMT (UTC) time instead of local
# time.  Default is to display the time the report was generated
# in the timezone of the local machine, such as EDT or PST.  This
# keyword allows you to have times displayed in UTC instead.  Use
# only if you really have a good reason, since it will probably
# screw up the reporting periods by however many hours your local
# time zone is off of GMT.

#GMTTime		no

# Debug prints additional information for error messages.  This
# will cause webalizer to dump bad records/fields instead of just
# telling you it found a bad one.   As usual, the value can be
# either "yes" or "no".  The default is "no".  It shouldn't be
# needed unless you start getting a lot of Warning or Error
# messages and want to see why.  (Note: warning and error messages
# are printed to stderr, not stdout like normal messages).

#Debug		no

# FoldSeqErr forces the Webalizer to ignore sequence errors.
# The Apache HTTP server may generate out-of-sequence log entries
# so this option is enabled.

FoldSeqErr	yes

# VisitTimeout allows you to set the default timeout for a visit
# (sometimes called a 'session').  The default is 30 minutes,
# which should be fine for most sites.
# Visits are determined by looking at the time of the current
# request, and the time of the last request from the site.  If
# the time difference is greater than the VisitTimeout value, it
# is considered a new visit, and visit totals are incremented.
# Value is the number of seconds to timeout (default=1800=30min)

#VisitTimeout	1800

# IgnoreHist shouldn't be used in a config file, but it is here
# just because it might be usefull in certain situations.  If the
# history file is ignored, the main "index.html" file will only
# report on the current log files contents.  Usefull only when you
# want to reproduce the reports from scratch.  USE WITH CAUTION!
# Valid values are "yes" or "no".  Default is "no".

#IgnoreHist	no

# Country Graph allows the usage by country graph to be disabled.
# Values can be 'yes' or 'no', default is 'yes'.

#CountryGraph	yes

# DailyGraph and DailyStats allows the daily statistics graph
# and statistics table to be disabled (not displayed).  Values
# may be "yes" or "no". Default is "yes".

#DailyGraph	yes
#DailyStats	yes

# HourlyGraph and HourlyStats allows the hourly statistics graph
# and statistics table to be disabled (not displayed).  Values
# may be "yes" or "no". Default is "yes".

#HourlyGraph	yes
#HourlyStats	yes

# GraphLegend allows the color coded legends to be turned on or off
# in the graphs.  The default is for them to be displayed.  This only
# toggles the color coded legends, the other legends are not changed.
# If you think they are hideous and ugly, say 'no' here :)

#GraphLegend	yes

# GraphLines allows you to have index lines drawn behind the graphs.
# I personally am not crazy about them, but a lot of people requested
# them and they weren't a big deal to add.  The number represents the
# number of lines you want displayed.  Default is 2, you can disable
# the lines by using a value of zero ('0').  [max is 20]
# Note, due to rounding errors, some values don't work quite right.
# The lower the better, with 1,2,3,4,6 and 10 producing nice results.

#GraphLines	2

# The "Top" options below define the number of entries for each table.
# Defaults are Sites=30, URL's=30, Referrers=30 and Agents=15, and
# Countries=30. TopKSites and TopKURLs (by KByte tables) both default
# to 10, as do the top entry/exit tables (TopEntry/TopExit).  The top
# search strings and usernames default to 20.  Tables may be disabled
# by using zero (0) for the value.

#TopSites        30
#TopKSites       10
#TopURLs         30
#TopKURLs        10
#TopReferrers    30
#TopAgents       15
#TopCountries    30
#TopEntry        10
#TopExit         10
#TopSearch       20
#TopUsers        20

# The All* keywords allow the display of all URL's, Sites, Referrers
# User Agents, Search Strings and Usernames.  If enabled, a seperate
# HTML page will be created, and a link will be added to the bottom
# of the appropriate "Top" table.  There are a couple of conditions
# for this to occur..  First, there must be more items than will fit
# in the "Top" table (otherwise it would just be duplicating what is
# already displayed).  Second, the listing will only show those items
# that are normally visable, which means it will not show any hidden
# items.  Grouped entries will be listed first, followed by individual
# items.  The value for these keywords can be either 'yes' or 'no',
# with the default being 'no'.  Please be aware that these pages can
# be quite large in size, particularly the sites page,  and seperate
# pages are generated for each month, which can consume quite a lot
# of disk space depending on the traffic to your site.

#AllSites	no
#AllURLs	no
#AllReferrers	no
#AllAgents	no
#AllSearchStr	no
#AllUsers       no

# The Webalizer normally strips the string 'index.' off the end of
# URL's in order to consolidate URL totals.  For example, the URL
# /somedir/index.html is turned into /somedir/ which is really the
# same URL.  This option allows you to specify additional strings
# to treat in the same way.  You don't need to specify 'index.' as
# it is always scanned for by The Webalizer, this option is just to
# specify _additional_ strings if needed.  If you don't need any,
# don't specify any as each string will be scanned for in EVERY
# log record... A bunch of them will degrade performance.  Also,
# the string is scanned for anywhere in the URL, so a string of
# 'home' would turn the URL /somedir/homepages/brad/home.html into
# just /somedir/ which is probably not what was intended.

#IndexAlias     home.htm
#IndexAlias	homepage.htm

# The Hide*, Group* and Ignore* and Include* keywords allow you to
# change the way Sites, URL's, Referrers, User Agents and Usernames
# are manipulated.  The Ignore* keywords will cause The Webalizer to
# completely ignore records as if they didn't exist (and thus not
# counted in the main site totals).  The Hide* keywords will prevent
# things from being displayed in the 'Top' tables, but will still be
# counted in the main totals.  The Group* keywords allow grouping
# similar objects as if they were one.  Grouped records are displayed
# in the 'Top' tables and can optionally be displayed in BOLD and/or
# shaded. Groups cannot be hidden, and are not counted in the main
# totals. The Group* options do not, by default, hide all the items
# that it matches.  If you want to hide the records that match (so just
# the grouping record is displayed), follow with an identical Hide*
# keyword with the same value.  (see example below)  In addition,
# Group* keywords may have an optional label which will be displayed
# instead of the keywords value.  The label should be seperated from
# the value by at least one 'white-space' character, such as a space
# or tab.
#
# The value can have either a leading or trailing '*' wildcard
# character.  If no wildcard is found, a match can occur anywhere
# in the string. Given a string "www.yourmama.com", the values "your",
# "*mama.com" and "www.your*" will all match.

# Your own site should be hidden
#HideSite	*mrunix.net
#HideSite	localhost

# Your own site gives most referrals
#HideReferrer	mrunix.net/

# This one hides non-referrers ("-" Direct requests)
#HideReferrer	Direct Request

# Usually you want to hide these
HideURL		*.gif
HideURL		*.GIF
HideURL		*.jpg
HideURL		*.JPG
HideURL		*.png
HideURL		*.PNG
HideURL		*.ra

# Hiding agents is kind of futile
#HideAgent	RealPlayer

# You can also hide based on authenticated username
#HideUser	root
#HideUser	admin

# Grouping options
#GroupURL	/cgi-bin/*	CGI Scripts
#GroupURL	/images/*	Images

#GroupSite	*.aol.com
#GroupSite	*.compuserve.com

#GroupReferrer	yahoo.com/	Yahoo!
#GroupReferrer	excite.com/     Excite
#GroupReferrer	infoseek.com/   InfoSeek
#GroupReferrer	webcrawler.com/ WebCrawler

#GroupUser      root            Admin users
#GroupUser      admin           Admin users
#GroupUser      wheel           Admin users

# The following is a great way to get an overall total
# for browsers, and not display all the detail records.
# (You should use MangleAgent to refine further...)

#GroupAgent	MSIE		Micro$oft Internet Exploder
#HideAgent	MSIE
#GroupAgent	Mozilla		Netscape
#HideAgent	Mozilla
#GroupAgent	Lynx*		Lynx
#HideAgent	Lynx*

# HideAllSites allows forcing individual sites to be hidden in the
# report.  This is particularly useful when used in conjunction
# with the "GroupDomain" feature, but could be useful in other
# situations as well, such as when you only want to display grouped
# sites (with the GroupSite keywords...).  The value for this
# keyword can be either 'yes' or 'no', with 'no' the default,
# allowing individual sites to be displayed.

#HideAllSites	no

# The GroupDomains keyword allows you to group individual hostnames
# into their respective domains.  The value specifies the level of
# grouping to perform, and can be thought of as 'the number of dots'
# that will be displayed.  For example, if a visiting host is named
# cust1.tnt.mia.uu.net, a domain grouping of 1 will result in just
# "uu.net" being displayed, while a 2 will result in "mia.uu.net".
# The default value of zero disable this feature.  Domains will only
# be grouped if they do not match any existing "GroupSite" records,
# which allows overriding this feature with your own if desired.

#GroupDomains	0

# The GroupShading allows grouped rows to be shaded in the report.
# Useful if you have lots of groups and individual records that
# intermingle in the report, and you want to diferentiate the group
# records a little more.  Value can be 'yes' or 'no', with 'yes'
# being the default.

#GroupShading	yes

# GroupHighlight allows the group record to be displayed in BOLD.
# Can be either 'yes' or 'no' with the default 'yes'.

#GroupHighlight	yes

# The Ignore* keywords allow you to completely ignore log records based
# on hostname, URL, user agent, referrer or username.  I hessitated in
# adding these, since the Webalizer was designed to generate _accurate_
# statistics about a web servers performance.  By choosing to ignore
# records, the accuracy of reports become skewed, negating why I wrote
# this program in the first place.  However, due to popular demand, here
# they are.  Use the same as the Hide* keywords, where the value can have
# a leading or trailing wildcard '*'.  Use at your own risk ;)

#IgnoreSite	bad.site.net
#IgnoreURL	/test*
#IgnoreReferrer	file:/*
#IgnoreAgent	RealPlayer
#IgnoreUser     root

# The Include* keywords allow you to force the inclusion of log records
# based on hostname, URL, user agent, referrer or username.  They take
# precidence over the Ignore* keywords.  Note: Using Ignore/Include
# combinations to selectivly process parts of a web site is _extremely
# inefficent_!!! Avoid doing so if possible (ie: grep the records to a
# seperate file if you really want that kind of report).

# Example: Only show stats on Joe User's pages...
#IgnoreURL	*
#IncludeURL	~joeuser*

# Or based on an authenticated username
#IgnoreUser     *
#IncludeUser    someuser

# The MangleAgents allows you to specify how much, if any, The Webalizer
# should mangle user agent names.  This allows several levels of detail
# to be produced when reporting user agent statistics.  There are six
# levels that can be specified, which define different levels of detail
# supression.  Level 5 shows only the browser name (MSIE or Mozilla)
# and the major version number.  Level 4 adds the minor version number
# (single decimal place).  Level 3 displays the minor version to two
# decimal places.  Level 2 will add any sub-level designation (such
# as Mozilla/3.01Gold or MSIE 3.0b).  Level 1 will attempt to also add
# the system type if it is specified.  The default Level 0 displays the
# full user agent field without modification and produces the greatest
# amount of detail.  User agent names that can't be mangled will be
# left unmodified.

#MangleAgents    0

# The SearchEngine keywords allow specification of search engines and
# their query strings on the URL.  These are used to locate and report
# what search strings are used to find your site.  The first word is
# a substring to match in the referrer field that identifies the search
# engine, and the second is the URL variable used by that search engine
# to define it's search terms.

SearchEngine	yahoo.com	p=
SearchEngine	altavista.com	q=
SearchEngine	google.com	q=
SearchEngine	eureka.com	q=
SearchEngine	lycos.com	query=
SearchEngine	hotbot.com	MT=
SearchEngine	msn.com		MT=
SearchEngine	infoseek.com	qt=
SearchEngine	webcrawler	searchText=
SearchEngine	excite		search=
SearchEngine	netscape.com	search=
SearchEngine	mamma.com	query=
SearchEngine	alltheweb.com	query=
SearchEngine	northernlight.com  qr=

# The Dump* keywords allow the dumping of Sites, URL's, Referrers
# User Agents, Usernames and Search strings to seperate tab delimited
# text files, suitable for import into most database or spreadsheet
# programs.

# DumpPath specifies the path to dump the files.  If not specified,
# it will default to the current output directory.  Do not use a
# trailing slash ('/').

#DumpPath	/var/log/httpd

# The DumpHeader keyword specifies if a header record should be
# written to the file.  A header record is the first record of the
# file, and contains the labels for each field written.  Normally,
# files that are intended to be imported into a database system
# will not need a header record, while spreadsheets usually do.
# Value can be either 'yes' or 'no', with 'no' being the default.

#DumpHeader	no

# DumpExtension allow you to specify the dump filename extension
# to use.  The default is "tab", but some programs are pickey about
# the filenames they use, so you may change it here (for example,
# some people may prefer to use "csv").

#DumpExtension	tab

# These control the dumping of each individual table.  The value
# can be either 'yes' or 'no'.. the default is 'no'.

#DumpSites	no
#DumpURLs	no
#DumpReferrers	no
#DumpAgents	no
#DumpUsers	no
#DumpSearchStr  no

# End of configuration file...  Have a nice day!
WEBALIZER;

		$indexConfig = <<<'INDEX'
<?php

$welcome = <<<EOT
<!DOCTYPE html>
<html><head><title>Welcome!</title></head>
<body>
	<p style="padding: 90px; font-size:24px; font-family: Verdana;">This is the default page for <u><span style="color:blue;">"{{ server_tag }}"</span></u></p>

</body></html>
EOT;

echo $welcome;
INDEX;

		DB::insert('insert into site_templates (type, content) values (?, ?)', array('nginx', $nginxConfig));
		DB::insert('insert into site_templates (type, content) values (?, ?)', array('apache', $apacheConfig));
		DB::insert('insert into site_templates (type, content) values (?, ?)', array('phpfpm', $phpfpmConfig));
		DB::insert('insert into site_templates (type, content) values (?, ?)', array('webalizer', $webalizerConfig));
		DB::insert('insert into site_templates (type, content) values (?, ?)', array('index', $indexConfig));

	}
}