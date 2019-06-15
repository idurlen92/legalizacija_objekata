<?php
	include '../classes/DatabaseHandler.php';
	include '../models/Model.php';
	include '../models/RequestsModel.php';

	$model = new RequestsModel(DatabaseHandler::getInstance('../config.ini'));
	$returnData = array('success' => true);

	if(!isset($_GET['id']) || !isset($_GET['type'])){
		$returnData['success'] = false;
		$returnData['desc'] = 'variables not set';
		echo json_encode($returnData);
		exit();
	}

	if($_GET['type'] == 'update'){
		$result = $model->select('request_status_id status_id')->where(array('id' => $_GET['id']))->exec();
		$status_id = 1 + $result[0]['status_id'];

		$returnData['success'] = $model->update(array('request_status_id' => $status_id), array('id' => $_GET['id']));
		if(!$returnData['success'])
			$returnData['desc'] = $model->getLastError();
	}

	echo json_encode($returnData);
	exit();