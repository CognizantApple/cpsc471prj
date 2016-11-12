<?php
module_load_include("php", "cpsc471prj", "includes/DBConf");
module_load_include("php", "cpsc471prj", "includes/DBTableInstance");

class Renter extends DBTableInstance{
	
	protected $id_p;
	protected $account_uid_p;
	protected $name_p;
	protected $is_adult_p;
	protected $birth_time_p;
	
	/**
	 * Does nothing
	 */
	public function Renter($type = 'new', $data = array()){
		$this->tableName = DBConf::$renter;
		parent::__construct($type, $data);
	}
	
	public function AddNewRenter($NewRenterInfo){
		$SQLQuery = "INSERT INTO ".DBConf::$renter . ' (';
		
		$values = '';
		
		foreach($NewRenterInfo as $column => $info) {
			$SQLQuery .= $column . ',';	
			$values .= $info . ',';
		}
		$SQLQuery = substr($SQLQuery, 0, strlen($SQLQuery) - 1);
		$values = substr($values, 0, strlen($values) - 1);
		
		$SQLQuery .= ' ) VALUES (' . $values . ')';
		try{
			db_query($SQLQuery);
		} catch(PDOException $e){
			drupal_set_message("Excpetion thrown while trying to add a new renter to the table.", 'error');
		}
	}
	
	
	
	
}







