<?php
	include '../classes/DatabaseHandler.php';
	include '../models/Model.php';
	include '../models/GradesModel.php';
	include '../models/RequestsModel.php';

	$returnData = array('success' => true);

	if(!isset($_GET['type'])) {
		$returnData['success'] = false;
		$returnData['desc'] = 'no type';

		echo json_encode($returnData);
		exit();
	}


	$gradesModel = new GradesModel(DatabaseHandler::getInstance('../config.ini'));
	$requestsModel = new RequestsModel(DatabaseHandler::getInstance('../config.ini'));

	if($_GET['type'] == 'insert'){
		$returnData['success'] = $gradesModel->insert(array(
			'user_id' => $_GET['user_id'],
			'constructor_id' => $_GET['constructor_id'],
			'grade' => $_GET['grade']
		));
		if(!$returnData['success'])
			$returnData['desc'] = $gradesModel->getLastError();
	}

	if($_GET['type'] == 'check'){
		$result1 = $requestsModel->select()->where(array(
			'user_id' => $_GET['user_id'], 'constructor_id' => $_GET['constructor_id']))->exec();
		$result2 = $gradesModel->select()->where(array(
			'user_id' => $_GET['user_id'], 'constructor_id' => $_GET['constructor_id']))->exec();

		$returnData['canGrade'] = ($result1 != null and $result2 == null) ? true : false;
	}


	echo json_encode($returnData);
	exit();