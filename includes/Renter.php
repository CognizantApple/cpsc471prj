<?php
module_load_include("php", "cpsc471prj", "includes/DBConf");

class Renter {
	
	/**
	 * Does nothing
	 */
	public function Renter(){ }
	
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







