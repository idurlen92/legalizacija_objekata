<?php

class CountiesModel extends Model{



	/**
	 * @param $dbHandler DatabaseHandler
	 */
	public function __construct($dbHandler) {
		$this->dbHandler = $dbHandler;
		$this->tableName = 'counties';
	}



	public function getAll() {
		return $this->dbHandler->executeSelect("SELECT * FROM counties ORDER BY 2 ASC;", array());
	}



	public function getColumns() {
        return array('id', 'name');
	}



	public function getJoinTables() {
		return array('users', 'terrains');
	}



	public function getJoinColumn($table, $preferedColumn = null) {
		$joinColumns = array(
			'users' => array('id', 'county_id'),
			'terrains' => array('id', 'county_id')
		);

		return $joinColumns[$table];
	}



}