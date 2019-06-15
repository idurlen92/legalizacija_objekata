<?php
	include '../classes/DatabaseHandler.php';
	include '../classes/Utils.php';
	include '../models/Model.php';
	include '../models/CommentsModel.php';
	include '../models/UsersModel.php';

	$commentsModel = new CommentsModel(DatabaseHandler::getInstance("../config.ini"));
	$returnData = array('success' => true);

	if(!isset($_POST['user_id']) or !isset($_POST['constructor_id']) or !isset($_POST['comment'])){
		$returnData['success'] = false;
		$returnData['desc'] = 'not all variables set';
		echo json_encode($returnData);
		exit();
	}

	$result = $commentsModel->executeRawQuery('SELECT CURRENT_TIMESTAMP timestamp;');
	$currentTimestamp = $result[0]['timestamp'];

	$fields = array('user_id' => $_POST['user_id'] ,
		'constructor_id' => $_POST['constructor_id'],
		'time' => $currentTimestamp ,
		'comment' => $_POST['comment']);
	$returnData['success'] = $commentsModel->insert($fields); //TODO: uncomment!

	if(!$returnData['success'])
		$returnData['desc'] = 'insert error: ' . $commentsModel->getLastError();
	else
		$returnData['timestamp'] = Utils::formatDateTime($currentTimestamp);

	echo json_encode($returnData);
	exit();