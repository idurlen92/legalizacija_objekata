<?php

class RequestStatusesModel extends Model{



	public function __construct(DatabaseHandler $dbHandler){
		$this->dbHandler = $dbHandler;
		$this->tableName = 'request_statuses';
	}



	public function getColumns() {
		return array('id', 'status_name');
	}


	public function getJoinTables() {
		return array('requests');
	}


	public function getJoinColumn($table, $preferedColumn = null) {
		$joinColumns = array(
			'requests' => array('id', 'request_status_id')
		);

		return $joinColumns[$table];
	}
}