<?php
	include '../classes/DatabaseHandler.php';
	include '../models/Model.php';
	include '../models/CountiesModel.php';


	$dataAll = array();
	$dataAll['success'] = true;

	if(!isset($_GET['type'])){
		$dataAll['success'] = false;
		$dataAll['desc'] = 'No type';
		echo json_encode($dataAll);
		exit();
	}

	$countiesModel = new CountiesModel(DatabaseHandler::getInstance('../config.ini'));

	if($_GET['type'] == 'fetch')
		$dataAll['countiesList'] = $countiesModel->select()->orderBy('name')->exec();
	else if($_GET['type'] == 'delete'){
		$dataAll['success'] = $countiesModel->deleteById($_GET['id']);
		if(!$dataAll['success'])
			$dataAll['desc'] = $countiesModel->getLastError();
	}
	else if($_GET['type'] == 'update'){
		$dataAll['success'] = $countiesModel->update(array('name' => $_GET['name']), array('id' => $_GET['id']));
		if(!$dataAll['success'])
			$dataAll['desc'] = $countiesModel->getLastError();
	}


	echo json_encode($dataAll);
	exit();