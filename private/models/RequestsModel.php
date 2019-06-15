<?php

class RequestsModel extends Model{


	public function __construct(DatabaseHandler $dbHandler){
		$this->dbHandler = $dbHandler;
		$this->tableName = 'requests';
	}



	public function getColumns() {
		return array(
			'id', 'date', 'description', 'user_id', 'constructor_id',
			'request_status_id', 'terrain_id'
		);
	}



	public function getJoinTables() {
		return array('users', 'terrains', 'resources', 'request_statuses');
	}


	public function getJoinColumn($table, $preferedColumn = 'user_id') {
		$joinColumns = array(
			'users' => array($preferedColumn, 'id'),
			'terrains' => array('terrain_id', 'id'),
			'resources' => array('id', 'request_id'),
			'request_statuses' => array('request_status_id', 'id')

		);

		return $joinColumns[$table];
	}



	public function insert($data){
		$date = $this->getDatabase()->executeSelect('SELECT CURRENT_DATE;', array());
		$data['date'] = $date[0]['CURRENT_DATE'];
		parent::insert($data);
	}



}