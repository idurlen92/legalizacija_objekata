<?php

class CommentsModel extends Model{


	public function __construct(DatabaseHandler $dbHandler){
		$this->dbHandler = $dbHandler;
		$this->tableName = 'comments';
	}


	public function getColumns() {
		return array('user_id', 'constructor_id', 'time', 'comment');
	}



	public function getJoinColumn($table, $preferedColumn = 'user_id') {
		$joinColumns = array(
			'users' => array($preferedColumn, 'id')
		);

		return $joinColumns[$table];
	}



	public function getJoinTables() {
		return array('users');
	}


}