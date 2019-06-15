<?php

class ActivationCodesModel extends Model{


	public function __construct(DatabaseHandler $dbHandler){
		$this->dbHandler = $dbHandler;
		$this->tableName = 'activation_codes';
	}


	public function getColumns() {
		return array('id', 'user_id', 'code', 'valid_until', 'activated');
	}



	public function getJoinTables() {
		return array('users');
	}



	public function getJoinColumn($table, $preferedColumn = null) {
		$joinColumns = array(
			'users' => array('user_id', 'id')
		);

		return $joinColumns[$table];
	}

}