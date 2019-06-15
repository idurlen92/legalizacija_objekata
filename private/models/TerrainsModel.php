<?php

class TerrainsModel extends Model{


	public function __construct(DatabaseHandler $dbHandler){
		$this->dbHandler = $dbHandler;
		$this->tableName = 'terrains';
	}


	public function getColumns() {
		return array(
			'id', 'field_area', 'building_area', 'field_number', 'cadastral_name',
			'county_id'
		);
	}



	public function getJoinTables() {
        return array('counties','requests');
	}


	public function getJoinColumn($table, $preferedColumn = null) {
		$joinColumns = array(
			'counties' => array('county_id', 'id'),
			'requests' => array('id', 'terrain_id')
		);

		return $joinColumns[$table];
	}
}