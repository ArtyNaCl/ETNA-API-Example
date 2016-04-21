<?php
	error_reporting(-1);				//DEBUG
	ini_set('display_errors', 'On');	//DEBUG
	ini_set('precision', '9');

	require_once __DIR__.'/../sources/vendor/autoload.php';
	require_once __DIR__.'/../sources/database.php';
	require_once __DIR__.'/geoloc.php';

	$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
	if (php_sapi_name() === 'cli-server' && is_file($filename))
   		return false;

	$app = new Silex\Application();
	$app['debug'] = true;				//DEBUG

	require(__DIR__.'/connexion.php');
	require(__DIR__.'/clients.php');
	require(__DIR__.'/providers.php');

	$app->run();