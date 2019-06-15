<?php

class Moderators implements View{

	private $controller;
	private $usersModel;

	public function __construct(UsersModel $usersModel, CountiesController $controller){
		$this->usersModel = $usersModel;
		$this->controller = $controller;
	}



	public function getOutput() {
		if($_SERVER['REQUEST_METHOD'] == 'POST')
			$this->controller->addModerator();

		$users = $this->usersModel->select()->where(array(
			'county_id' => $_GET['id'],
			'user_type_id' => 2,
			'active' => 't'
		))->orderBy(array('name, surname'))->exec();

		$display = "";
		if($users != null){
			$display .= "<div id=\"moderatorsDiv\"><ul>";
			foreach($users as $currentUser){
				$display .= "<li><ul>";
				$display .= "<li style=\"display: inline-block; width: 150px;\">" . $currentUser['surname'] . " "
						. $currentUser['name'] . "</li>";
				$display .= "<li style=\"display: inline-block;\"><a onclick=\"removeModerator(this);\">Ukloni</a></li>";
				$display .= "</ul></li>";
			}
			$display .= "</ul></div>";
		}
		else
			$display = "<h5>Županija nema moderatora</h5>";

		echo $display;

		// --------- Regular users ----------
		$regularUsers = $this->usersModel->select()->where(array(
			'county_id' => $_GET['id'],
			'user_type_id' => 3,
			'active' => 't'
		))->orderBy(array('name, surname'))->exec();

		$form = "<div id=\"divAddCounty\"><form id=\"formAddModerator\" method=\"POST\"><div id=\"moderatorInput\">";
		$form .= "<label for=\"selectRegularUsers\">Korisnici</label><select id=\"selectRegularUsers\" name=\"selectRegularUsers\">";
		$form .= "<option value=\"-\">-</option>";
		foreach($regularUsers as $regUser)
			$form .= "<option value=\"" . $regUser['id'] . "\">" . $regUser['surname'] . " " . $regUser['name'] . "</option>";
        $form .= "</select>";
		$form .= "<input type=\"submit\" id=\"buttonConfirm\" value=\"Dodaj moderatora\"></div></form></div>";

		echo $form;
	}
}