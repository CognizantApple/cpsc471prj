<?php

module_load_include("php", "cpsc471prj", "includes/database/DBConf");
module_load_include("php", "cpsc471prj", "includes/database/DBTableInstance");


class BoatItemRate extends DBTableInstance {
	
	protected $start_time_p;
	protected $duration_p;
	protected $item_id_p;
	protected $price_p;
	protected $end_time_p;
	
	/**
	 * Construct a BoatItemRate by passing it some values
	 */
	public function BoatItemRate($type = 'new', $data = array()){
		$this->tableName = DBConf::$boatRentalItemRate;
		parent::__construct($type, $data);
	}
}