<?php
	require_once 'medoo.php';

	$database = new medoo([
	    'database_type' => 'mysql',
	    'database_name' => 'jolicut',
	    'server' => 'localhost',
	    'username' => 'root',
	    'password' => 'CCiosgrtards',
	    'charset' => 'utf8'
	]);