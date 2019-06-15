<?php

class GradesModel extends Model{


	public function __construct(DatabaseHandler $dbHandler){
		$this->dbHandler = $dbHandler;
		$this->tableName = 'grades';
	}


	public function getColumns() {
		return array('user_id', 'constructor_id', 'grade');
	}



	public function getJoinTables() {
		return array('users');
	}



	public function getJoinColumn($table, $preferedColumn = 'user_id') {
		$joinColumns = array(
			'users' => array($preferedColumn, 'id')
		);

		return $joinColumns[$table];
	}



	public function getAverageGrade($constructorId){
		$avgGrade = $this->dbHandler->executeSelect("SELECT SUM(grade)/COUNT(*) avg FROM grades "
			. "WHERE constructor_id = ?;", array($constructorId));
		return $avgGrade == null ? null : $avgGrade[0]['avg'];
	}

}