<?php
	session_start();

	if($_SERVER['HTTP_HOST'] == 'english'){
		$db_info = [
			'db' => 'english',
			'user' => 'root',
			'pass' => 'm00c0wz88'
		];
	}else if ($_SERVER['HTTP_HOST'] == 'englishlessonhub.com' || $_SERVER['HTTP_HOST'] == 'www.englishlessonhub.com') {
		$db_info = [
			'db'	=> 'englis06_elh',
			'user' => 'englis06_elh',
			'pass' => "B99^o9tw2N;i"
		];
	} else if ($_SERVER['HTTP_HOST'] == '5ppdev.com' || $_SERVER['HTTP_HOST'] == 'www.5ppdev.com'){
		$db_info = [
			'db'	=> 'justin28_english',
			'user' => 'justin28_english',
			'pass' => 'ZRJT9b=%%yJT'
		];
	}

	$admin_info = [
		'username' 	=> 'admin',
		'password' 	=> 'elh2016!',
		'email'		=> 'robbybauer@hotmail.com'
	];

	$billing_info = [
		'percent' 	=> 8.6,
		'add'		=> 1,
		'maxFreeClasses' => 5
	];

	$pp_config = [
		'mode' => 'live',
		'acct1.UserName' => 'robbybauer_api1.hotmail.com',
		'acct1.Password' => 'QF8HKXQQJLB2NMS2',
		'acct1.Signature' => 'Aww0oyur6.mR-CdnjKoRmPVCeHDBASA0Lgz0ilBzKh3059KTyB7u4iMG'
	];

	$pp_app_config = [
		'id' => 'AbKxI0qChgXyn7HGzmZ5ex3-_ynHcuxowk0he3TqHxhb3MYCWRK4drPlFumYGZI2tC7XFZXgdC9KM68Q',
		'secret' => 'EAdZToQDUSkzf0SA5CeqMwe-8McukfLzjEKcYXGTVbCGWV4jINbnz9Ty-iSdrMwfhTTufVRv_rwCXg7Q'
	];

	$mailConfig = [
			'server' => 'securees18.sgcpanel.com',
			'username' => 'admin@englishlessonhub.com',
			'password' => 'english_A!',
			'securityType' => 'admin@englishlessonhub.com',
			'port' => '465',
			'from' => 'admin@englishlessonhub.com',
	];


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

	//require('PayPal-PHP-SDK/autoload.php');

	require ('vendor/autoload.php');

	$apiContext = getApiContext($pp_app_config['id'], $pp_app_config['secret'], $pp_config['mode'] == 'sandbox');
	check_ip();
	listen_for_cookie();
