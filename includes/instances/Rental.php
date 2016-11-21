<?php

module_load_include("php", "cpsc471prj", "includes/database/DBConf");
module_load_include("php", "cpsc471prj", "includes/database/DBTableInstance");
/**
 * Class for making and adding seasons
 * @author andys
 *
 */
class Rental extends DBTableInstance {

	
	protected $start_time_p;

	
	protected $renters_uid_p;
	
	
	protected $duration_p;
	
	protected $actual_arrival_time_p;
	
	protected $estimated_arrival_time_p;
	
	protected $returning_p;
	
	protected $rental_type_p;


	/**
	 * Construct a renter by passing it some values
	 */
	public function Rental($type = 'new', $data = array()){
		$this->tableName = DBConf::$rental;
		parent::__construct($type, $data);
	}
	
	public function loadRentalAccount() {
		
	}
	
	public function loadRentedItems() {
		
	}
}