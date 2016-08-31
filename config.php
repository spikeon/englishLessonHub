<?php
	session_start();

	require_once('vendor/autoload.php');
	require_once('class/Logging.class.php');

	$dotenv = new Dotenv\Dotenv(__DIR__);
	$dotenv->load();

	$main_log = new Logging();
	$chron_log = new Logging();
	$paypal_log = new Logging();

	$main_log->lfile('./mainlog.txt');
	$chron_log->lfile('./chronlog.txt');
	$paypal_log->lfile('./paypallog.txt');

	$db_info = [ 'db' => $_ENV['db_name'], 'user' => $_ENV['db_user'], 'pass' => $_ENV['db_password'] ];

	$admin_info = [ 'username' => $_ENV['admin_username'], 'password' => $_ENV['admin_password'], 'email' => $_ENV['admin_email'] ];

	$billing_info = [
		'percent' 	=> 8.6,
		'add'		=> 1,
		'maxFreeClasses' => 5
	];

	$pp_config = [
		'mode' => 'live',
		'acct1.UserName' => $_ENV['pp_username'],
		'acct1.Password' => $_ENV['pp_password'],
		'acct1.Signature' => $_ENV['pp_signature']
	];

	$pp_app_config = [ 'id' => $_ENV['pp_app_id'], 'secret' => $_ENV['pp_app_secret'] ];

	$mailConfig = [
			'server' => $_ENV['mail_out_server'],
			'username' => $_ENV['mail_out_username'],
			'password' => $_ENV['mail_out_password'],
			'securityType' => 'ssl',
			'port' => '465',
			'from' => $_ENV['mail_out_username'],
			'from_name' => $_ENV['mail_out_from']
	];

	$date_format = "d M Y";

	$db = new PDO("mysql:host=localhost;dbname={$db_info['db']};charset=utf8", $db_info['user'], $db_info['pass']);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	define("ELH_DEBUG", true);

	require_once('phpThumb/phpthumb.functions.php');
	require_once('phpThumb/phpthumb.class.php');
	$phpThumb = new phpThumb();
	if (include_once('phpThumb/phpThumb.config.php')) {
		foreach ($PHPTHUMB_CONFIG as $key => $value) {
			$keyname = 'config_'.$key;
			$phpThumb->setParameter($keyname, $value);
		}
	}

	if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
		$protocol = 'http://';
	} else {
		$protocol = 'https://';
	}
	$base_url = $protocol . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']);

	define('BASE_URL', $base_url);

	include('class/teacher.class.php');
	include('class/student.class.php');
	include('functions.php');

	$apiContext = getApiContext($pp_app_config['id'], $pp_app_config['secret']);
	check_ip();
	listen_for_cookie();
