<?php

class ResourceTypesModel extends Model{


	public function __construct(DatabaseHandler $dbHandler){
		$this->dbHandler = $dbHandler;
		$this->tableName = 'resource_types';
	}



	public function getColumns() {
		return array('id', 'type_name');
	}


	public function getJoinTables() {
		return array('resources');
	}


	public function getJoinColumn($table, $preferedColumn = null) {
		$joinColumns = array(
			'resources' => array('id', 'resource_type_id')
		);

		return $joinColumns[$table];
	}
}