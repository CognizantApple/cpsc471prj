<?php
module_load_include("php", "cpsc471prj", "includes/database/DBConf");
module_load_include("php", "cpsc471prj", "includes/database/DBTableInstance");

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

