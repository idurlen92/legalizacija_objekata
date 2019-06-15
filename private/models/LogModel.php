<?php

class LogModel extends Model{


	public function __construct(DatabaseHandler $dbHandler){
		$this->dbHandler = $dbHandler;
		$this->tableName = 'log';
	}


	public function getColumns() {
		return array('id', 'user_id', 'log_type_id', 'time', 'description');
	}



	public function getJoinTables() {
		return array('users', 'log_types');
	}



	public function getJoinColumn($table, $preferedColumn = null) {
		$joinColumns = array(
			'users' => array('user_id', 'id'),
			'log_types' => array('log_type_id','id')
		);

		return $joinColumns[$table];
	}

}