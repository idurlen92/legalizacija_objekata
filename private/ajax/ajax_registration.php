<?php
	include '../classes/DatabaseHandler.php';
	$dbHandler = DatabaseHandler::getInstance('../config.ini');

	$column = '';
	if(isset($_GET['username']))
		$column = 'username';
	else if(isset($_GET['email']))
		$column = 'email';

	$exists = $dbHandler->executeSelect("SELECT id FROM users WHERE {$column} = ?;", array($_GET[$column]));

	if($exists == null)
		echo json_encode(array("exists" => "false"));
	else
		echo json_encode(array("exists" => "true"));

	exit();