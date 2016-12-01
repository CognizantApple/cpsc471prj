<?php
module_load_include("php", "cpsc471prj", "includes/database/DBConf");
module_load_include("php", "cpsc471prj", "includes/database/DBTableInstance");

class Renter extends DBTableInstance{
	
	protected $id_p;
	protected $account_uid_p;
	protected $name_p;
	protected $is_adult_p;
	protected $birth_time_p;
	
	/**
	 * Does nothing
	 */
	public function __construct($type = 'new', $data = array()){
		$this->tableName = DBConf::$renter;
		parent::__construct($type, $data);
	}
	
	
}







