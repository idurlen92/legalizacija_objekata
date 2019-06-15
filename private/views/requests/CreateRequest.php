<?php

class CreateRequest implements View{

	/**@var CountiesModel */
	private $countiesModel = null;
	/** @var RequestsModel  */
	private $requestsModel = null;
	/** @var TerrainsModel  */
	private $terrainsModel = null;

	/** @var RequestController  */
	private $controller = null;


	public function __construct(RequestsModel $requestsModel, TerrainsModel $terrainsModel, RequestController $controller){
		$this->requestsModel = $requestsModel;
		$this->terrainsModel = $terrainsModel;
		$this->countiesModel = new CountiesModel($this->requestsModel->getDatabase());
		$this->controller = $controller;
	}



	public function getOutput() {
		if($_SERVER['REQUEST_METHOD'] == 'POST')
			$this->controller->sendRequest($_POST);
		else{
			$form = " <form id=\"form_request\" name=\"form_request\" method=\"POST\" onsubmit=\"return isFormValid();\">
				        <fieldset>
				        	<div>
				                <label for=\"input_field_number\">Broj čestice</label>
				                <input type=\"number\" id=\"input_field_number\" name=\"input_field_number\"/>
				            </div>
				            <div>
				                <label for=\"input_cadastral_name\">Katastarska općina</label>
				                <input type=\"text\" id=\"input_cadastral_name\" name=\"input_cadastral_name\">
				            </div>
				            <div>
				                <label for=\"input_field_area\">Površina parcele</label>
				                <input type=\"number\" id=\"input_field_area\" name=\"input_field_area\"/>
				            </div>
				            <div>
				                <label for=\"input_building_area\">Površina objekta</label>
				                <input type=\"number\" id=\"input_building_area\" name=\"input_building_area\"/>
				            </div>
				            <div>
				                <label for=\"select_county\">Županija</label>
				                <select id=\"select_county\" name=\"select_county\" form=\"form_request\">";

				$form .= "<option value=\"-\">-</option>";
				foreach($this->countiesModel->select()->orderBy('name')->exec() as $county)
					$form .= '<option value="' . $county['id'] . '">' . $county['name'] . '</option>' ;

				$form .= "</select></div>";
				$form .= "<div>
							<label for=\"select_constructor\">Građevinar</label>
			                <select id=\"select_constructor\" name=\"select_constructor\" form=\"form_request\">
			                	<option value=\"-\"> - </option>
							</select>
			            </div>
			            <div>
			            	<label for=\"input_description\">Opis</label>
			            	<textarea id=\"input_description\" name=\"input_description\" form=\"form_request\" rows=\"4\" cols=\"40\" wrap=\"soft\"></textarea>
						</div>
						<input type=\"submit\" value=\"Potvrdi\"/>
					</fieldset>
				</form>";

			echo "<h1>Novi zahtjev</h1>";
			echo $form;
		}
	}


}