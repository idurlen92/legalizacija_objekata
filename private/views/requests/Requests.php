<?php

class Requests implements View{

	private $controller;

	private $requestsModel;
	private $terrainsModel;

	private $requestStatuses;


	public function __construct(RequestsModel $requestsModel, TerrainsModel $terrainModel, RequestController $controller){
		$this->requestsModel = $requestsModel;
		$this->terrainsModel = $terrainModel;
		$this->controller = $controller;

		$requestStatusesModel = new RequestStatusesModel($requestsModel->getDatabase());
		$this->requestStatuses = $requestStatusesModel->select()->orderBy(array('id'))->exec();

		foreach($this->requestStatuses as $key => $value)
			$this->requestStatuses[$value['id']] = $value['status_name'];
	}



	private function createSubList($requests){
		$subList = "";

		if(empty($requests))
			$subList .= "<h5>- nema zahtjeva</h5>";
		else{
			$subList .= "<ul>";
			foreach($requests as $currentRequest){
				$terrain = $this->terrainsModel->getByKey($currentRequest['terrain_id']);
				$type = User::isModerator() ? "edit" : "view";

				$subList .= "<li>[" . Utils::formateDate($currentRequest['date']) . "] ";
				$subList .= "<a href=\"?action=requests&type={$type}&id={$currentRequest['id']}\">";
				$subList .= "Teren " . $terrain['cadastral_name'] . " čestica " . $terrain['field_number'];
				$subList .= "</a></li>";
			}
			$subList .= "</ul>";
		}

		return $subList;
	}



	public function getOutput(){
		$requestStatuses = array(
			'sent' => 'Poslani', 'accepted' => 'Prihvaćeni',
			'processed' => 'Obrađeni', 'confirmed' => 'Potvrđeni');

		$userColumn = User::isRegularUser() ? 'user_id' : 'constructor_id';

		$requestsAll = array();
		$k=1;
		foreach($requestStatuses as $key => $val){
			$requestsAll[$key] = $this->requestsModel->select()->where(array($userColumn => User::getUserId(),
				'request_status_id' => $k++))->exec();
		}

		$displayList = "<ul>";
		foreach($requestStatuses as $key => $value){
			$displayList .= "<li><a onclick=\"toggleContent(this);\"><h3>" . $value. " zahtjevi:</h3></a>";
			$displayList .= "<div>";
			$displayList .= $this->createSubList($requestsAll[$key]);
			$displayList .= "</div></li>";
		}
		if(User::isModerator()){
			$constructorCountyId = $this->requestsModel->executeRawQuery("SELECT county_id FROM USERS WHERE id = ?;",
				array(User::getUserId()));
			$constructorCountyId = $constructorCountyId[0]['county_id'];

			$criteria = array(
				'r.constructor_id' => 'comparison:!=;value:' . User::getUserId(),
				't.county_id' => $constructorCountyId);
			$otherRequests = $this->requestsModel->select('r.*, t.cadastral_name, t.field_number')->join(array('terrains t'), 'r')->where($criteria)->exec();
			$displayList .= "<li><a onclick=\"toggleContent(this);\"><h3>Ostali zahtjevi:</h3></a>";
			$displayList .= "<div>";
			$displayList .= $this->createSubList($otherRequests);
			$displayList .= "</div></li>";
		}
		$displayList .= "</ul>";

		echo $displayList;
		if(User::isRegularUser())
			echo "<input type=\"button\" id=\"buttonNewRequest\" value=\"Novi zahtjev\"/>";
	}


}