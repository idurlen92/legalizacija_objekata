<?php

class ModelFactory {

	public $modelNames = null;

	/**@var ModelFactory */
	private static $instance = null;


	private function __construct(){
		$this->modelNames = Model::getAllTables();
	}

	private function __clone(){/*Prevented*/}


	public static function getInstance(){
		if(self::$instance === null)
			self::$instance = new self();
		return self::$instance;
	}


	public function getModel($name){
		$model = null;

		if(in_array($name, $this->modelNames)){
			$modelName = "";
			$nameParts = explode('_', $name);
			foreach($nameParts as $part)
				$modelName .= ucfirst($part);
			$modelName .= "Model";

			if(Utils::findString(getcwd(), 'ajax'))
				$model = new $modelName(DatabaseHandler::getInstance('../config.ini'));
			else
				$model = new $modelName(DatabaseHandler::getInstance());
		}
		else
			error_log("Error - no such model: " . $name, 0);

		return $model;
	}


}