<?php
	require_once 'medoo.php';

	$database = new medoo([
	    'database_type' => 'mysql',
	    'database_name' => /*NAME*/,
	    'server' => 'localhost',
	    'username' => 'root',
	    'password' => /*PASSWD*/,
	    'charset' => 'utf8'
	]);