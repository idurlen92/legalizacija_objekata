<?php

class RequestController {

	private $requestsModel = null;
	private $resourcesModel = null;
	private $terrainsModel = null;


	public function __construct(ResourcesModel $resourcesModel, RequestsModel $requestsModel, TerrainsModel $terrainsModel){
		$this->resourcesModel = $resourcesModel;
		$this->requestsModel = $requestsModel;
		$this->terrainsModel = $terrainsModel;
	}



	public function sendRequest($data){
		$terrainData = array(
			'field_area' => $data['input_field_area'],
			'building_area' => $data['input_building_area'],
			'field_number' => $data['input_field_number'],
			'cadastral_name' => $data['input_cadastral_name'],
			'county_id' => $data['select_county']
		);
		$this->terrainsModel->insert($terrainData);

		$terrainId = $this->requestsModel->getInsertId();

		$requestData = array(
			'description' => $data['input_description'],
			'user_id' => User::getUserId(),
			'constructor_id' => $data['select_constructor'],
			'terrain_id' => $terrainId
		);

		$this->requestsModel->insert($requestData);
		Utils::redirect('requests');
	}



	public function uploadFile($requestId){
		if(!Utils::requestDirectoryExists($requestId))
			Utils::makeRequestDirectory($requestId);

		$filePath = Utils::getRequestDirectoryPath($requestId) . basename($_FILES['inputFile']['name']);

		if(file_exists($filePath))
			echo "<h5>Datoteka već postoji</h5>";
		else{
			$fileType = 2;
			if(strcmp('application/pdf', $_FILES['inputFile']['type']) == 0)
				$fileType = 1;
			else if(strcmp('video/mp4', $_FILES['inputFile']['type']) == 0)
				$fileType = 3;

			if (move_uploaded_file($_FILES['inputFile']['tmp_name'], $filePath)){
				//TODO: check size
				$this->resourcesModel->insert(array(
					'path' => $filePath, 'resource_type_id' => $fileType,
					'request_id' => $requestId
				));
				echo "<h5>Datoteka pohranjena<h5>";
			}
			else
				echo "<h5>Greška!<h5>";
		}//else[1]
	}

}