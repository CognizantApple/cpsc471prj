<?php

module_load_include("php", "cpsc471prj", "includes/database/DBConf");
module_load_include("php", "cpsc471prj", "includes/database/DBTableInstance");
/**
 * Class for making and adding seasons
 * @author andys
 *
 */
class PricedFor extends DBTableInstance {
	
	/**
	 * The id of the cottage
	 * @var int
	 */
	protected $cottage_id_p;
	
	/**
	 * The start time of the season. (PK)
	 * @var int
	 */
	protected $season_start_time_p;
	
	/**
	 * The id of the cost guide
	 * @var int
	 */
	protected $cost_guide_id_p;
	
	/**
	 * Construct a renter by passing it some values
	 */
	public function PricedFor($type = 'new', $data = array()){
		$this->tableName = DBConf::$pricedFor;
		parent::__construct($type, $data);
	}
	
}