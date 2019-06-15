<?php

class Constructors implements View{

	private $model = null;

	public function __construct(CountiesModel $model){
		$this->model = $model;
	}


	public function getOutput(){
		$counties = $this->model->select()->orderBy('name')->exec();

		$form = "<div id=\"divCounty\"><label for=\"selectCounties\">Županija</label><select id=\"selectCounties\" name=\"\">";
		$form .= "<option value=\"-\">-</option>";
		foreach($counties as $county)
			$form .= "<option value=\"" . $county['id'] . "\">" . $county['name'] . "</option>";
        $form .= "</select></div>";

		echo $form;
	}

}