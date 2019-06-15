<?php

class DataController {

	private $model = null;

	public function __construct(Model $model){
		$this->model = $model;
	}


	private function printInfo($success){
		$info =  "<div class=\"" . ($success ? "operation_success" : "operation_fail") . "\">Opearacija ";
		$info .= ($success ? 'uspješno' : 'neuspješno') . " izvršena</div>";
		echo $info;
	}


	public function actionDelete(){
		$rows = null;
		if(!isset($_GET['column']))
			$rows = $this->model->select()->exec();
		else {
			$sort = $_GET['column'] . ' ' . (isset($_GET['order']) ? $_GET['order'] : 'asc');
			$rows = $this->model->select()->orderBy($sort)->exec();
		}

		$criteria = array();
		$k = 0;
		foreach($_POST['checkedRows'] as $key => $val)
			$criteria[$k++] = $rows[$val];

		$success = $this->model->deleteMultiple($criteria);
		$this->printInfo($success);
	}


	public function actionInsert(){
		$columns = $this->model->getColumns();
		$data = array();

		$i = 0;
		foreach($this->model->getColumns() as $column){
			$i++;
			$data[$column] = $_POST[('crudInput' . $i)];
		}

		$success = $this->model->insert($data);
		$this->printInfo($success);
	}


}