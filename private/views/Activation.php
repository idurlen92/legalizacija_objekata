<?php

class Activation implements View{

	/**@var ActivationCodesModel*/
	private $model = null;
	/**@var ActivationController*/
	private $controller = null;

	public function __construct(ActivationCodesModel $model, ActivationController $controller){
		$this->model = $model;
		$this->controller = $controller;
	}


    public function getOutput(){
	    $this->controller->actionCheck();
	    echo "<br/><h5><a href=\"?action=index\">Povratak na naslovnicu</a></h5>";
    }


}