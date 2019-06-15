<?php

class CountiesController {

	private $model = null;

	public function __construct(Model $model){
		$this->model = $model;
	}


	public function addCounty(){
		if(strlen($_POST['inputCounty']) > 0){
			if($this->model->insert(array('name' => $_POST['inputCounty'])))
				echo "<div class=\"operation_success\">Uspješan unos</div>";
			else
				echo "<div class=\"operation_fail\">Greška prilikom unosa</div>";
		}
	}


	public function addModerator(){
		if(strcmp($_POST['selectRegularUsers'], '-') !== 0){
			if($this->model->update(array('user_type_id' => 2), array('id' => $_POST['selectRegularUsers'])))
				echo "<div class=\"operation_success\">Uspješna dodjela moderatora</div>";
			else
				echo "<div class=\"operation_fail\">Greška prilikom dodjele moderatora</div>";
		}
	}

}