<?php


class UsersModel extends Model {


    /**
     * @param $dbHandler DatabaseHandler
     */
    public function __construct($dbHandler) {
        $this->dbHandler = $dbHandler;
        $this->tableName = 'users';
    }



	public function getJoinTables() {
		return array('counties', 'user_types', 'activation_codes', 'grades', 'comments',
			'log', 'requests');
	}



	public function getJoinColumn($table, $preferedColumn = 'user_id') {
		$joinColumns = array(
			'counties' => array('county_id','id'),
			'user_types' => array('user_type_id', 'id'),
			'activation_codes' => array('id', 'user_id'),
			'grades' => array('id', $preferedColumn),
			'comments' => array('id', $preferedColumn),
			'log' => array('id', 'user_id'),
			'requests' => array('id', 'user_id')
		);

		return $joinColumns[$table];
	}



	public function getColumns(){
        return array(
            'id', 'name', 'surname', 'gender', 'birth_date', 'username', 'password',
            'email', 'city', 'address', 'user_type_id', 'county_id', 'active'
        );
    }



	public function getByKey($key) {
		$query = "SELECT * FROM users WHERE active = ? AND id = ?;";
		$result = $this->dbHandler->executeSelect($query, array('t', $key));
		return $result == null ? null : $result[0];
	}

}