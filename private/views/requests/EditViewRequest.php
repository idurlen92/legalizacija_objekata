<?php

class EditViewRequest implements View{

	/**@var ResourcesModel */
	private $resourcesModel = null;
	/** @var RequestsModel  */
	private $requestsModel = null;
	/** @var TerrainsModel  */
	private $terrainsModel = null;

	/** @var RequestController  */
	private $controller = null;


	public function __construct(ResourcesModel $resourcesModel, RequestsModel $requestsModel, TerrainsModel $terrainsModel,
	                            RequestController $controller){
		$this->resourcesModel = $resourcesModel;
		$this->requestsModel = $requestsModel;
		$this->terrainsModel = $terrainsModel;
		$this->controller = $controller;
	}



	public function getOutput(){
		if($_SERVER['REQUEST_METHOD'] == 'POST')
			$this->controller->uploadFile($_GET['id']);

		$subQuery = "(SELECT co.name FROM counties co WHERE co.id = t.county_id) county";
		$columns = array(
			'r.date', 'r.description', 'rs.id status_id' ,'rs.status_name status', 'c.id c_id', 'u.id u_id',
			"concat(c.name, ' ',  c.surname) constructor", "concat(u.name, ' ', u.surname) user",
			't.*', $subQuery
		);
		$joins = array('request_statuses rs', 'users u;user_id', 'users c;constructor_id', 'terrains t');
		$result = $this->requestsModel->select($columns)->join($joins, 'r')->where(array('r.id' => $_GET['id']))->exec();

		if($result == null){
			$view = new Site404();
			$view->getOutput();
			return;
		}

		$request = $result[0];
		if(empty($request['description']))
			$request['description'] = '-';
		$columns = array(
			'date' => 'Datum upućenja', 'user' => 'Korisnik',
			'constructor' => 'Građevinar', 'status' => 'Status zahtjeva',
			'description' => 'Opis zahtjeva', 'county' => 'Županija terena',
			'cadastral_name' => 'Katastarska općina', 'field_number' => 'Broj čestice',
			'field_area' => 'Površina terena', 'building_area' => 'Površina objekta'
		);

		// ---------- 1) Table  ----------
		$table = "<table border='1'><caption>Informacije o zahtjevu</caption>";
		foreach($columns as $key => $value){
			$table .= "<tr><td>" . $value . "</td>";
			$table .= "<td>". (strcmp($key, 'date') == 0 ? Utils::formateDate($request[$key], true) : $request[$key]) . "</td></tr>";
		}
		$table .= "</table>";
		echo $table;

		// ---------- 2) Accepting/confirming request ----------
		$value = User::isModerator() ? 'Prihvati zahtjev' : 'Potvrdi zahtjev';
		$requestButton = "<input type=\"button\" id=\"buttonAcceptConfirm\" value=\"" . $value . "\"><br/>";
		if((User::isModerator() and $request['status_id'] == 1 and User::getUserId() == $request['c_id'])
			or (User::isRegularUser() and $request['status_id'] == 3 and User::getUserId() == $request['u_id']))
				echo $requestButton;

		// ---------- 3) Gallery  ----------
		// ************** TODO **************
		$resources = $this->resourcesModel->select()->where(array('request_id' => $_GET['id']))->exec();
		$gallery = "<div class=\"gallery\">";
		foreach($resources as $currentResource){
			$gallery .= "<div class=\"gallery_element\">";
			$pathSplitted = explode('/', $currentResource['path']);
			$name = $pathSplitted[count($pathSplitted)-1];

			if($currentResource['resource_type_id'] == 2)
				$gallery .= "<img src=\"" . $currentResource['path'] . "\" alt=\"" . $name . "\"/>";
			$gallery .= "</div>";
		}
		$gallery .= "</div>";
		echo $gallery;

		// ---------- 4) File uploader ----------
		$uploadForm = "<form id=\"form_upload\" name=\"form_upload\" method=\"POST\" enctype=\"multipart/form-data\">
			    <div>
			        <input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"5000000\" />
					<input type=\"file\" accept=\".jpg,.jpeg,.png,.mp4,application/pdf\" name=\"inputFile\" id=\"inputFile\"><br/>
                    <input type=\"submit\" value=\"Potvrdi\">

			    </div>
			</form>";
		if(User::isModerator() and $request['c_id'] == User::getUserId())
			echo $uploadForm;
	}


}