<?php

class ResourcesModel extends Model{


	public function __construct(DatabaseHandler $dbHandler){
		$this->dbHandler = $dbHandler;
		$this->tableName = 'resources';
	}


	public function getColumns() {
		return array(
			'id', 'path', 'resource_type_id', 'request_id'
		);
	}



	public function getJoinTables() {
		return array('requests', 'resource_types');
	}



	/**
	 * @param $table
	 * @return array - prvi argument je stupac tablice djeteta, drugi od tablice roditelja
	 */
	public function getJoinColumn($table, $preferedColumn = null) {
		$joinColumns = array(
			'requests' => array('request_id', 'id'),
			'resource_types' => array('resource_type_id', 'id')
		);

		return $joinColumns[$table];
	}
}