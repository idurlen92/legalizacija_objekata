<?php

class UserTypesModel extends Model{



	public function __construct(DatabaseHandler $dbHandler){
		$this->dbHandler = $dbHandler;
		$this->tableName = 'user_types';
	}



	public function getColumns() {
		return array('id', 'type_name');
	}


	public function getJoinTables() {
		return array('users');
	}


	public function getJoinColumn($table, $preferedColumn = null) {
		$joinColumns = array(
			'users' => array('id', 'user_type_id')
		);

		return $joinColumns[$table];
	}
}