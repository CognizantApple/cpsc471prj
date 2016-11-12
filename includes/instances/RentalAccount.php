<?php
module_load_include("php", "cpsc471prj", "includes/database/DBConf");
module_load_include("php", "cpsc471prj", "includes/database/DBTableInstance");

/**
 * The rental account for creating rentals from
 * @author Patrick
 */
class RentalAccount extends DBTableInstance{

	/**
	 * The uid of the drupal account this rental account is accessed through
	 * @var int
	 */
	protected $uid_p;
	
	/**
	 * The credit card number associated with this account
	 * @var string | int
	 */
	protected $credit_card_p;
	
	/**
	 * The phone number associated with this account
	 * @var string | int
	 */
	protected $phone_p;
	
	/**
	 * The id of the primary renter for this account (from DBConf::$renter)
	 * @var int
	 */
	protected $primary_renter_id_p;

	/**
	 * Sets table and calls parent ctor
	 */
	public function __construct($type = 'new', $data = array()){
		$this->tableName = DBConf::$rentalAccount;
		parent::__construct($type, $data);
	}
}




