<?php

class Counties implements View{

	private $controller;
	private $countiesModel;
	private $usersModel;

	public function __construct(CountiesModel $countiesModel, UsersModel $usersModel, CountiesController $controller){
		$this->countiesModel = $countiesModel;
		$this->usersModel = $usersModel;
		$this->controller = $controller;
	}



	public function getOutput() {
		if($_SERVER['REQUEST_METHOD'] == 'POST')
			$this->controller->addCounty();

		$counties = $this->countiesModel->select()->orderBy('name')->exec();

		$list = "<ul id=\"listCounties\">";
		foreach($counties as $currentCounty) {
			$list .= "<li style=\"margin: 5px 0;\"><ul>";
			$list .= "<li style=\"display: inline-block; width: 250px;\">";
			$list .= "<a href=\"?action=counties&id=" .  $currentCounty['id'] . "\">" . $currentCounty['name'] . "</a></li>";
			$list .= "<li style=\"display: inline-block; margin-right: 1em;\"><a onclick=\"editCounty(this);\">Uredi</a></li>";
			$list .= "<li style=\"display: inline-block;\"><a  onclick=\"deleteCounty(this);\">Izbriši</a></li>";
			$list .= "</ul></li>";
		}
		$list .= "</ul>";

		$form = "<div id=\"divAddCounty\">
			        <form id=\"formInsertCounty\" method=\"POST\">
				        <div id=\"divCountyInput\">
				            <label for=\"inputCounty\">Naziv</label>
				            <input type=\"text\" id=\"inputCounty\" name=\"inputCounty\"><br/>
				            <input type=\"submit\" id=\"buttonConfirm\" value=\"Dodaj županiju\">
				        </div>
			        </form>
			    </div>";

		echo $list;
		echo $form;
	}
}