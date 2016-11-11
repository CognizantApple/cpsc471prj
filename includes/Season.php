<?php

module_load_include("php", "cpsc471prj","includes/DBconf");
module_load_include("php", "cpsc471prj","includes/DBTableInstance");
/**
 * Class for making and adding seasons
 * @author andys
 *
 */
class Season extends DBTableInstance {
	
	protected $id_p;
	
	/**
	 * Construct a renter by passing it some values
	 */
	public function Season($type = 'new', $data = array()){
		$this->tableName = DBConf::$season;
		parent::__construct($type, $data);
	}
	
	/**
	 * adds the stuff in $values into da table
	 * @param unknown $values
	 
	public function addSeason($values){
		$myQuery = "INSERT INTO ". DBConf::$season. " VALUES ".
				"("
	}
	*/
}