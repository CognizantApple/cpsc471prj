<?php

/**
 * Base class for instances from tables
 * 
 * All variables that exist as column names should end with _p
 * 
 * when you extend the class, set $this->tableName = DBConf::$theCorrecTable
 * 
 * @author Patrick
 *
 */
abstract class DBTableInstance {
	/**
	 * The name of the table this instance belongs to
	 * @var string
	 */
	protected $tableName;
	
	/**
	 * Initializes a instance from a table
	 * @param string $type
	 * 		'new' => creates an empty instance
	 * 		'standard' => loads an instance using $data
	 * @param array $data
	 * 		an associative array of 
	 * 		<column_name> => <column_value>, when loading using  method 'standard'
	 * 		these values will be used in a select query to load the first matching row
	 * 		(if there are multiple matching rows, no promises are made about which one is loaded)
	 */
	public function __construct($type = 'new', $data = array()) {
		if($type == 'new') {
			return;
		} else if($type == 'standard') {
			$this->setCopy($data);
			$this->getFromDB();
		}
	}
	
	
	/**
	 * Loads all matching instances that match $data and returns them
	 * in a 0 indexed array
	 * 
	 * Note: This function should use minimal db calls,
	 * at most 1 per table, except under really weird circumstances
	 * 
	 * @param array $data
	 * 		an associative array of 
	 * 		<column_name> => <column_value>
	 * 		all rows that match all of the provided names will be loaded
	 * 		note that calling with the default argument will load all rows
	 * 
	 * @return DBTableInstance[]
	 * 		The loaded results, 0 indexed
	 */
	public static function instanceLoadMultiple($data = array()) {
		//get the class that called this
		$class = get_called_class();
		//create an instance of that class
		$sample = new $class();

		//create a select query
		$arguments = array();
		$sql = $sample->selectQuery($data, $arguments);
		
		//exectute the query
		$result = db_query($sql, $arguments);
		
		$instances = array();
		
		//create an instance from each result
		while($row = $result->fetchAssoc()) {
			$instance = new $class();
			$instance->setCopy($row);
			$instances[] = $instance;
		}
			
		//return the loaded results
		return $instances;
	}
	
	/**
	 * Checks all persistent (_p) variables to see if a row in the table exists
	 * with that name
	 * 
	 * @return bool
	 * 		true if the instance exists, false otherwise
	 */
	public function existsInDB() {
		return $this->getFromDB();
	}
	
	/**
	 * Creates the current instance as a new row in the database,
	 * @throws Exception
	 * 		If the instance already exists in the database
	 */
	public function storeToDB() {
		//get all the set persistant variables
		$toStore = $this->getPersistentNotNull();
		//if none were set, throw an exception
		if(count($toStore) == 0) {
			throw new PDOException("No member variables have been set");
		}
		//create the insert query
		$sql = 'INSERT INTO ' . $this->tableName;
		
		$colNames = '(';
		$colValues = '(';
		//this prevents having a comma at the end of the list
		$commaTrip = false;
		
		$parameters = array();
		
		foreach($toStore as $name => $value) {
			if($commaTrip) {
				$colNames .= ', ';
				$colValues .= ', ';
			}
			
			$colNames .= $name;
			//? for parameterized query
			$colValues .= '?';
			
			$parameters[] = $value;
						
			$commaTrip = true;
		}
		
		$colNames .= ')';
		$colValues .= ')';
		
		//assembe the sections
		$sql = $sql . $colNames . ' VALUES ' . $colValues;
		//execute the query
		db_query($sql, $parameters);
		//reload ourselves to fetch default values
		$this->getFromDB();
	}
	
	/**
	 * Updates the instance in the database
	 * using the values in the object to find an instance
	 * and the values in $toUpdate as replacements,
	 * 
	 * Replaces member variables with updated values on success
	 * 
	 * @param array $toUpdate
	 * 		<col_name> => <col_value>
	 */
	public function updateInDB($toUpdate) {
		//get the persistent vars to select on
		$toSelect = $this->getPersistentNotNull();
		
		//no point in doing a query if there is nothing to update
		if(count($toUpdate) == 0) {
			throw new PDOException("Nothing provided to update with");
		}
		
		
		//arguments for the parameterized query
		$arguments = array();
		//base of the query
		$sql = 'UPDATE ' . $this->tableName . ' SET ';
		//list of conditions
		$condition = ' WHERE ';
		
		if(count($toSelect) == 0) {
			$condition .= '1'; //update where 1
		}
		
		$commaTrip = false;
		//create the list of things to change
		foreach($toUpdate as $column => $value) {
			if($commaTrip) {
				$sql .= ', ';
			}
			
			$sql .= $column . '=?';
			$arguments[] = $value;
			$commaTrip = true;
		}
		
		$andTrip = false;
		//create the list of conditions to update
		foreach($toSelect as $column => $value) {
			if($andTrip) {
				$condition .= 'AND ';
			}
			
			$condition .= $column . '=? ';
			$arguments[] = $value;
			$andTrip = true;
		}
		
		//assemble the query
		$sql .= $condition;
		//execute
		db_query($sql, $arguments);
		//update ourselves now
		$toUpdate += $toSelect;
		$this->setCopy($toUpdate);
	}
	
	/**
	 * Attempts to load an instance from the database
	 * using the currently set members, loads the values into this object
	 * 
	 * @return bool
	 * 		true on success, false on failure
	 */
	public function getFromDB() {
		//get the set persistent members
		$toSelect = $this->getPersistentNotNull();
		
		if(count($toSelect) == 0) {
			throw new PDOException("No member variables have been set");
		}
		
		//pass by reference to fill
		$arguments = array();
		$sql = $this->selectQuery($toSelect, $arguments);
		
		//execute the query and fetch some row from the result
		$result = db_query($sql, $arguments);	
		
		$row = $result->fetchAssoc();
			
		if($row === false) {
			return false;
		}
		//if there was a result, set ourselves to a copy of it
		$this->setCopy($row);
		
		return true;
	}
	
	/**
	 * Creates an SQL select query to select by the provided fields
	 * @param array $toSelect
	 * 		An associative array of <field_name> => <field_value>
	 * @param array $arguments 
	 * 		An output argument to fill with the arguments to the select query
	 */
	protected function selectQuery($toSelect, &$arguments) {
		$sql = 'SELECT * FROM ' . $this->tableName . ' WHERE ';
		
		$commaTrip = false;
		
		$arguments = array();
		
		if(count($toSelect) == 0) {
			$sql .= ' 1'; // WHERE 1 (where true)
			return $sql;
		}
		
		foreach($toSelect as $index => $value) {
			if($commaTrip) {
				$sql .= ' AND ';
			}
				
			$sql .= $index . '=?';
			$arguments[] = $value;
			$commaTrip = true;
		}
		
		return $sql;
	}
	
	
	/**
	 * Setter for all member variables
	 * @param string $key
	 * 		The member to access
	 * @param mixed $value
	 * 		The value to set there
	 */
	public function setProperty($key, $value) {
		if(property_exists($this, $key)) {
			$this->$key = $value;
			return;
		}
		
		$keyP = $key . '_p';
		//add _p to $key and try again
		if(property_exists($this, $keyP)) {
			$this->$keyP = $value;
			return;
		}
		
		//if the key didn't exist, throw an exception
		throw new InvalidArgumentException("$key doesn't exist in the object");
	}
	
	/**
	 * Getter for all member variables
	 * @param string $key
	 * 		The member to access
	 * @return mixed
	 * 		The value of said member
	 */
	public function getProperty($key) {
		if(property_exists($this, $key)) {
			return $this->$key;
		}
		
		$keyP = $key . '_p';
		//add _p to $key and try again	
		if(property_exists($this, $keyP)) {
			return $this->$keyP;
		}
		
		//otherwise throw an exception
		throw new InvalidArgumentException("$key doesn't exist in the object.");
	}
	
	/**
	 * Creates an associative array of all member variables
	 * @return array
	 */
	public function toArray() {
		return get_object_vars($this);
	}
	
	/**
	 * Sets this object to a copy of an associative array
	 * or another object
	 * @param array | object $toCopy
	 * 		The object to copy
	 * 		Either another instance of this class,
	 * 		or an associative array of $member => $value
	 */
	public function setCopy($toCopy) {
		//convert to array if required
		if(is_object($toCopy)) {
			$toCopy = $toCopy->toArray();
		}
		
		//iterate over the things to copy
		foreach($toCopy as $key => $value) {
			try {
				$this->setProperty($key, $value);
			} catch (InvalidArgumentException $e) {
				//key didn't exist or wasn't a valid variable name
				//just continue with the next one
			}
		}
	}
	
	/**
	 * Gets all member variables that end with _p
	 */
	private function getPersistentVars() {
		//get the members
		$vars = $this->toArray();
		
		$persistent = array ();
		//iterate over each result, and if it ends with '_p' add it the result array,
		//without the _p
		foreach($vars as $name => $value) {
			if(substr($name, strlen($name) - 2) == '_p') {
				$name = substr($name, 0, strlen($name) - 2);
				$persistent[$name] = $value;
			}
		}
		
		return $persistent;
	}
	
	/**
	 * Gets all persistent variables that aren't null
	 */
	private function getPersistentNotNull() {
		//get the persistent vars
		$persistent = $this->getPersistentVars();
		
		//foreach var, check if its null
		foreach($persistent as $index => $value) {
			if($value === null) {
				unset($persistent[$index]);
			}
		}
		
		return $persistent;
	}
	
	/**
	 * Creates a new instance in the database if the current instance doesn't exist already
	 * @return bool
	 * 		true on success, false on failure
	 */
	public function create() {
		if($this->existsInDB()) {
			return false;
		}
		
		$this->storeToDB();
		return true;
	}
	
	
}