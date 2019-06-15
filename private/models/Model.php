<?php

abstract class Model {

    /**@var DatabaseHandler */
    protected $dbHandler = null;

	protected $tableName = "";
	protected $selectStatement = "";
	protected $whereStatement = "";
	protected $otherStatements = "";
	protected $whereArguments = null;


	public abstract function getColumns();
	public abstract function getJoinColumn($table, $preferedColumn = null);
	public abstract function getJoinTables();


	/**
	 * Creates array of arguments based on given criteria array.
	 * @param $criteria - array of key/value pairs
	 * @return array
	 */
	protected  function  createArgumentsArray($criteria){
		$arguments = array();

		$i=0;
		foreach($criteria as $key => $value)
			$arguments[$i++] = $value;

		//var_dump($arguments);
		return $arguments;
	}



	/**
	* Creates sequence separated with comma, based on array argument. Used for appending Select statement.
	* @param $arguments string | array of values
	* @return null|string created on given sequence array
	*/
	protected function appendSequence($arguments){
		if($arguments == null or empty($arguments))
			return null;

		if(!is_array($arguments))
			$arguments = array(0 => $arguments);

		$sequence = '';
		$length = count($arguments);

		for($i=0; $i < $length; $i++){
			$sequence .= $arguments[$i];
			$sequence .= ($i < ($length - 1)) ? ', ' : ' ';
		}// for

		return $sequence;
	}



	/**
	 * Creates where clause based on given criteria array(AND operator used!).
	 * @param $criteria - array of key/value pairs
	 * @return string
	 */
	protected function appendWhereStatement($criteria, $prefix = null){
		$query = "";

		$i = 0;
		$length = count($criteria);
		foreach($criteria as $key => $value){
			if($prefix != null)
				$query .= $prefix . " .";
			$query .= $key . " = ?";
			$query .= ($i++ < ($length - 1)) ? ' AND ' : ';';
		}

		return $query;
	}


	/**
	 * @param array|string $columns
	 * @return $this
	 */
	public function select($columns = null){
		$this->selectStatement .= 'SELECT ';

		if($columns == null or empty($columns))
			$this->selectStatement .= '* ';
		else
			$this->selectStatement .= $this->appendSequence($columns);

		$this->selectStatement .= "FROM " . $this->getTableName() . " ";

		return $this;
	}



	/**
	 * Prvi parametar moze biti string odjeljen s ';' gdje je prvi dio tablica i alias, a drugi dio zeljeni
	 * stupac za spajanje, npr. 'comments c;constructor_id'
	 * @param array|string $tables
	 * @param $alias
	 * @return $this
	 */
	public function join($tables, $alias){
		if($tables == null or empty($tables))
			return $this;

		$this->selectStatement .= ' ' . $alias . ' ';
		if(!is_array($tables))
			$tables = array(0 => $tables);

		foreach($tables as $t){
			$joinTable = $t;

			$parts = explode(';', $t);
			if(count($parts) > 1)
				$joinTable = $parts[0];
			$this->selectStatement .= "JOIN " . $joinTable . " ON ";

			$subParts = explode(' ', $joinTable);
			$parentAlias = (count($subParts) == 2) ? $subParts[1] : $this->getTableName();
			$parentAlias .= '.';

			if(count($parts) > 1)
				$joinColumns = $this->getJoinColumn($subParts[0], $parts[1]);
			else
				$joinColumns = $this->getJoinColumn($subParts[0]);

			$this->selectStatement .= $alias . '.' . $joinColumns[0] . " = " . $parentAlias . $joinColumns[1] . ' ';
		}


		return $this;
	}



	/**
	 * Example for mixed string: 'comparison:!=;logic:OR;value:ivica'
	 * @param $criteria
	 * @return $this
	 */
	public function where($criteria){
		$this ->whereStatement .= 'WHERE ';
		$this->whereArguments = array();

		$i = 0;
		foreach($criteria as $key => $val){
			$logicOperator = 'AND';
			$comparisonOperator = '=';
			$expression = '?';
			$isInOperator = false;

			if(strpos($val, ';') !== false){
				$parts = explode(';', $val);

				foreach($parts as $currentPart){
					$subParts = explode(':', $currentPart, 2);
					if(strcmp($subParts[0], 'comparison') == 0){
						$comparisonOperator = $subParts[1];
						$isInOperator = (stripos($comparisonOperator, 'IN') !== false);
					}
					else if(strcmp($subParts[0], 'logic') == 0)
						$logicOperator = $subParts[1];
					else{
						if($isInOperator){
							$valueParts = explode(',', $subParts[1]);
							$expression = '';

							for($k=0; $k<count($valueParts); $k++){
								$this->whereArguments[$i + $k] = $valueParts[$k];
								$valueParts[$k] = '?';
							}

							$i += count($valueParts);
							$expression = $this->appendSequence($valueParts);
						}
						else
							$this->whereArguments[$i] = $subParts[1];
					}
				}// foreach
			}
			else
				$this->whereArguments[$i] = $val;

			if($i > 0)
				$this->whereStatement .= $logicOperator . ' ';
			$this->whereStatement .= $key . ' ' . $comparisonOperator . ($isInOperator ? '(' : ' ');
			$this->whereStatement .= $expression . ($isInOperator ? ') ' : ' ');

			$i++;
		}

		return $this;
	}


	/**
	 * @param $columns array or string/integer
	 * @return $this
	 */
	public function orderBy($columns){
		$this->otherStatements .= " ORDER BY ";
		$this->otherStatements .= is_array($columns) ? $this->appendSequence($columns) : $columns;
		return $this;
	}



	/**
	 * @param int $val
	 * @return $this
	 */
	public function limit($val){
		$this->otherStatements .= ' LIMIT ' . $val;
		return $this;
	}


	/**
	 * @param int $val
	 * @return $this
	 */
	public function offset($val){
		$this->otherStatements .= ' OFFSET ' . $val;
		return $this;
	}


	/**
	 * Mandatory to execute the SELECT statement.
	 * @return array|null
	 */
	public function exec(){
		$fullStatement = $this->selectStatement . ' ' . $this->whereStatement . $this->otherStatements . ';';
		$result =  $this->dbHandler->executeSelect($fullStatement, $this->whereArguments);

		//echo $fullStatement;
		//var_dump($this->whereArguments);

		$this->selectStatement = '';
		$this->whereStatement = '';
		$this->otherStatements = '';
		$this->whereArguments = null;

		return $result;
	}


	public static function getAllTables(){
		return array(
			'activation_codes', 'comments', 'counties', 'grades',
			'log', 'log_type', 'requests', 'request_statuses',
			'resources', 'resource_types', 'terrains', 'users',
			'user_types'
		);
	}



	/**
	 * @return \DatabaseHandler
	 */
    public function getDatabase(){
        return $this->dbHandler;
    }



	/**
	 * @param $id
	 * @return bool
	 */
	public function deleteById($id){
		$statement = "DELETE FROM {$this->tableName} WHERE id = ?;";
		return $this->dbHandler->execNonSelect($statement, array($id));
	}



	/**
	 * @param array key/value pairs of criteria
	 * @return bool success of transaction.
	 */
	public function deleteMultiple(array $rows){
		if(!$this->dbHandler->beginTransaction())
			return;

		$success = true;
		foreach($rows as $rowCriteria){
			$statement = "DELETE FROM " . $this->tableName . " WHERE " . $this->appendWhereStatement($rowCriteria);
			$arguments = $this->createArgumentsArray($rowCriteria);
			if(!$this->dbHandler->execNonSelect($statement, $arguments)){
				$success = false;
				break;
			}
		}

		if(!$success or !$this->dbHandler->commitTransaction()){
			$this->dbHandler->rollBackTransaction();
			return false;
		}
		return true;
	}



	/**
	 * Rarely needed to execute raw query.
	 * @param $query
	 * @param array $arg
	 * @return array|null
	 */
	public function executeRawQuery($query, $arg = array()){
		return $this->dbHandler->executeSelect($query, $arg);
	}



	/**
	 * Returns id of last inserted row.
	 * @return string
	 */
	public function getInsertId(){
		return $this->dbHandler->getLastInsertId();
	}



	/**
	 * Returns last handled error.
	 * @return null|string
	 */
	public function getLastError(){
		return $this->dbHandler->getLastError();
	}


	public function getTableName(){
		return $this->tableName;
	}


    public function getByKey($key){
        $result = $this->dbHandler->executeSelect("SELECT * FROM {$this->tableName} WHERE id = ?;", array($key));
	    return $result == null ? null : $result[0];
    }


	/**
	 * @param $data
	 * @return bool
	 */
    public function insert($data){
        $statement = "INSERT INTO {$this->tableName} (";

        $i = 0;
        $length = count($data);
        foreach($data as $key => $value){
            $statement .= $key;
	        $statement .= ($i++ < ($length - 1)) ? ', ' : ') ';
        }

        $statement .= 'VALUES(';
        for($i=0; $i < $length; $i++)
            $statement .= ($i < ($length - 1)) ? '?, ' : '?);';

        //echo $statement . '<br/>';
	    //var_dump($this->createArgumentsArray($data));

        return $this->dbHandler->execNonSelect($statement, $this->createArgumentsArray($data));
    }



	/**
	 * Simple update statement.
	 * @param $data key/value pairs array, containing all column names and values
	 * @param $criteria key/value pairs array: example: id => 1;
	 * @return bool
	 */
	public function update($data, $criteria){
		$statement = "UPDATE " . $this->getTableName() . " SET ";

		$arguments = array();
		$i=0;
		foreach($data as $key => $value){
			$statement .= $key . " = ? ";
			if($i < count($data) - 1)
				$statement .= ', ';
			$arguments[$i++] = $value;
		}
		$statement .= "WHERE " . $this->appendWhereStatement($criteria);

		$criteriaArray = $this->createArgumentsArray($criteria);
		foreach($criteriaArray as $key => $value)
			$arguments[$i++] = $value;

		//echo $statement . "<br/>";
		//var_dump($arguments);

		return $this->dbHandler->execNonSelect($statement, $arguments);
	}



}