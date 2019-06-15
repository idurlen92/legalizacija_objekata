<?php
	include '../classes/DatabaseHandler.php';
	include '../models/Model.php';
	include '../models/UsersModel.php';

	$model = new UsersModel(DatabaseHandler::getInstance('../config.ini'));
	$returnData = array('success' => true);

	if(!isset($_GET['username']) || !isset($_GET['password'])){
		$returnData['success'] = false;
		$returnData['desc'] = 'variables not set';
		echo json_encode($returnData);
		exit();
	}

	$user = $model->select(array('u.*', 'ut.type_name user_type'))->join('user_types ut', 'u')->where(array(
		'username' => $_GET['username'], 'password' => $_GET['password'], 'active' => 't'
		))->exec();
	if($user == null){
		$returnData['success'] = false;
		$returnData['desc'] = 'no user';
	}
	else {
		$returnData['userId'] = $user[0]['id'];
		$returnData['userType'] = $user[0]['user_type'];
	}

	echo json_encode($returnData);
	exit();