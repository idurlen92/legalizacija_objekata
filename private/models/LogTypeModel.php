<?php

class LogTypeModel extends Model{

	public function __construct(DatabaseHandler $dbHandler){
		$this->dbHandler = $dbHandler;
		$this->tableName = 'log_types';
	}


	public function getColumns() {
		return array('id', 'log_name');
	}



	public function getJoinTables() {
		return array('log');
	}



	public function getJoinColumn($table, $preferedColumn = null) {
		$joinColumns = array(
			'log' => array('id', 'log_type_id')
		);

		return $joinColumns[$table];
	}

}