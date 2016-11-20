<?php

module_load_include("php", "cpsc471prj", "includes/database/DBConf");
module_load_include("php", "cpsc471prj", "includes/database/DBTableInstance");
/**
 * Class for making and adding seasons
 * @author andys
 *
 */
class Season extends DBTableInstance {
	
	/**
	 * The start time of the season.
	 * serves as primary key.
	 * @var int
	 */
	protected $start_time_p;
	
	/**
	 * The end time of the season.
	 * @var int
	 */
	protected $end_time_p;
	
	/**
	 * The name of the season
	 * @var varchar(60)
	 */
	protected $name_p;
	
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