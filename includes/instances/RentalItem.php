<?php
module_load_include("php", "cpsc471prj", "includes/database/DBConf");
module_load_include("php", "cpsc471prj", "includes/database/DBTableInstance");
module_load_include('php', 'cpsc471prj', 'includes/instances/BoatItemRate');
module_load_include('php', 'cpsc471prj', 'includes/instances/Season');
module_load_include('php', 'cpsc471prj', 'includes/instances/CottagePriceGuide');
module_load_include('php', 'cpsc471prj', 'includes/instances/PricedFor');

class RentalItem extends DBTableInstance{
	
	/**
	 * The rental Items ID
	 * @var int
	 */
	protected $id_p;
	
	/**
	 * Is this item still active?
	 * @var int
	 */
	protected $is_active_p;
	
	/**
	 * Calls parent ctor after setting table
	 */
	public function __construct($type = 'new', $data = array()){
		$this->tableName = DBConf::$rentableItem;
		parent::__construct($type, $data);
	}
	
}

