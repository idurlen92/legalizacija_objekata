<?php
	include '../classes/DatabaseHandler.php';
	include '../models/Model.php';
	include '../models/UsersModel.php';
	include '../models/RequestsModel.php';
	include '../models/ResourcesModel.php';


	$returnData = array('success' => 'true');
	if(!isset($_GET['county'])) {
		$returnData['success'] = false;
		$returnData['desc'] = 'no county';
		echo json_encode($returnData);
		exit();
	}


	$userModel = new UsersModel(DatabaseHandler::getInstance('../config.ini'));
	$requestModel = new RequestsModel(DatabaseHandler::getInstance('../config.ini'));
	$resourceModel = new ResourcesModel(DatabaseHandler::getInstance('../config.ini'));

	// 1) get all constructors
	$constructors = $userModel->select()->where(array(
		'county_id' => $_GET['county'],
		'user_type_id' => 2
	))->exec();

	if($constructors != null){
		$returnData['data'] = array();
		// 2) iterate over all constructors
		for($i=0; $i<count($constructors); $i++){
			$data = array();
			foreach($constructors[$i] as $key => $value)
				$data[$key] = $value;

			//2.1) check requests of this constructor
			$requests = $requestModel->select()->where(array('constructor_id' => $constructors[$i]['id']))->exec();
			if($requests == null){
				$data['images'] = 0;
				$data['videos'] = 0;
				$data['documents'] = 0;
			}
			else{
				//2.2) count resources
				$images = 0;
				$videos = 0;
				$documents = 0;

				foreach($requests as $request){
					$result = $resourceModel->select('COUNT(*) n')->where(array(
						'request_id' => $request['id'],'resource_type_id' => 1))->exec();
					$documents += $result[0]['n'];

					$result = $resourceModel->select('COUNT(*) n')->where(array(
						'request_id' => $request['id'],'resource_type_id' => 2))->exec();
					$images += $result[0]['n'];

					$result = $resourceModel->select('COUNT(*) n')->where(array(
						'request_id' => $request['id'],'resource_type_id' => 3))->exec();
					$videos += $result[0]['n'];
				}

				$data['images'] = $images;
				$data['videos'] = $videos;
				$data['documents'] = $documents;
			}//else[1]


			$returnData['data'][$i] = $data;
		}// foreach
	}

	echo json_encode($returnData);
	exit();