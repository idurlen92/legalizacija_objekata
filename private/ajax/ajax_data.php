<?php
	include '../../includes.php';
	includeFiles('../classes/');
	includeFiles('../models/');

	$returnData = array('success' => true);

	if($_SERVER['REQUEST_METHOD'] != 'POST'){
		$returnData['success'] = false;
		$returnData['desc'] = 'wrong request';
		echo json_encode($returnData);
		exit();
	}
	else if (!isset($_POST['table'])) {
		$returnData['success'] = false;
		$returnData['desc'] = 'no table';
		echo json_encode($returnData);
		exit();
	}


	$factory = ModelFactory::getInstance();
	$model = $factory->getModel($_POST['table']);

	$rows = null;
	if(!isset($_POST['column']))
		$rows = $model->select()->exec();
	else{
		$sort = $_POST['column'] . ' ' . (isset($_POST['order']) ? $_POST['order'] : 'asc');
		$rows = $model->select()->orderBy($sort)->exec();
	}

	$targetRow = $rows[$_POST['rowNumber'] - 1];
	$postedData = $_POST['data'];


	$columns = $model->getColumns();
	$data = array();
	$k = 0;
	foreach($postedData as $value)
		$data[$columns[$k++]] = $value;

	$returnData['success'] = $model->update($data, $targetRow);
	if(!$returnData['success'])
		$returnData['desc'] = $model->getLastError();

	echo json_encode($returnData);
	exit();