<?php

module_load_include("php", "cpsc471prj", "includes/database/DBConf");
module_load_include("php", "cpsc471prj", "includes/database/DBTableInstance");


class CottagePriceGuide extends DBTableInstance {
	
	protected $id_p;
	protected $name_p;
	protected $two_day_p;
	protected $three_day_p;
	protected $week_p;
	protected $rebook_discount_p;
	
	
	/**
	 * Construct a renter by passing it some values
	 */
	public function CottagePriceGuide($type = 'new', $data = array()){
		$this->tableName = DBConf::$cottagePriceGuide;
		parent::__construct($type, $data);
	}
}