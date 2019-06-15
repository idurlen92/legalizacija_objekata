<?php

class Data implements View{

	/** @var Model  */
	private $activeModel = null;
	/** @var DataController */
	private $controller = null;
	private $tableNames = null;

	public function __construct(){
		$this->tableNames = Model::getAllTables();

		$modelFactory = ModelFactory::getInstance();
		if(isset($_GET['table'])){
			$this->activeModel = $modelFactory->getModel($_GET['table']);
			$this->controller = new DataController($this->activeModel);
		}
	}



	private function createSelections(){
		//TODO: class
		$tableSelection = "<div class=\"#\"><label for=\"selectTables\">Tablica</label><select id=\"selectTables\">";
		$tableSelection .= "<option value=\"-\"> - </option>";
		foreach($this->tableNames as $key => $val)
			$tableSelection .= "<option value=\"" . $val . "\">" . $val . "</option>";
		$tableSelection .= "</select></div>";

		return $tableSelection;
	}



	private function fillTable(){
		if($this->activeModel === null)
			return;

		$table = "<table id=\"activeTable\" border=\"1\"><thead>";
		$tableColumns = $this->activeModel->getColumns();
		foreach($tableColumns as $column)
			$table .= "<td>" . $column . "</td>";
		$table .= "</thead><tbody>";

		$rows = null;
		if(!isset($_GET['column']))
			$rows = $this->activeModel->select()->exec();
		else{
			$sort = $_GET['column'] . ' ' . (isset($_GET['order']) ? $_GET['order'] : 'asc');
			$rows = $this->activeModel->select()->orderBy($sort)->exec();
		}

		$i=0;
		foreach($rows as $row){
			$table .= "<tr>";
			foreach($tableColumns as $column)
				$table .= "<td>" . $row[$column] . "</td>";
			$table .= "</tr>";
			$i++;
		}

		$table .= "</tbody></table>";

		return $table;
	}


	private function createBottomTable(){
		$table = "<div style=\"overflow: scroll;\" class=\"\">";
		if(isset($_GET['table']))
			$table .= $this->fillTable();
		else
			$table .= "<h4>Nema ničega!</h4>";
		$table .= "</div>";

		return $table;
	}



	public function getOutput() {
		if($_SERVER['REQUEST_METHOD'] === 'POST'){
			if(isset($_POST['insert']))
				echo $this->controller->actionInsert();
			else if(isset($_POST['delete']))
				echo $this->controller->actionDelete();
		}
		else if(isset($_GET['operation'])){
			$info =  "<div class=\"" . ($_GET['operation'] == 'success' ? "operation_success" : "operation_fail") . "\">Opearacija ";
			$info .= ($_GET['operation'] == 'success' ? 'uspješno' : 'neuspješno') . " izvršena</div>";
			echo $info;
		}

		$display = "<form id=\"form_crud\" name=\"form_crud\" method=\"POST\" >";
		$display .= $this->createSelections();
		$display .= $this->createBottomTable();
		$display .= "</form>";

		echo $display;
	}



}