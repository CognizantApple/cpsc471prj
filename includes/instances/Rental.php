<?php

module_load_include("php", "cpsc471prj", "includes/database/DBConf");
module_load_include("php", "cpsc471prj", "includes/database/DBTableInstance");
/**
 * Represents a DB tuple for a rental
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
	
	protected $has_been_confirmed_p;
	
	/**
	 * The rental account that made this rental, only loaded after $this->loadRentalAccount()
	 * @var RentalAccount
	 */
	protected $rentalAccount;
	
	/**
	 * An array of the rented items, indexed by id, only loaded after $this->loadRentedItems();
	 * @var Cottage[] | BoatItem[]
	 */
	protected $rentedItems;
	
	/**
	 * An array of renters, only loaded after $this->loadRenters(); indexed by their ID
	 * @var Renter[]
	 */
	protected $renterInstances;
	


	/**
	 * Construct a renter by passing it some values
	 */
	public function Rental($type = 'new', $data = array()){
		$this->tableName = DBConf::$rental;
		parent::__construct($type, $data);
	}
	
	/**
	 * Loads the rental account that created this rental
	 */
	public function loadRentalAccount() {
		$this->rentalAccount = new RentalAccount('standard', array(
			'uid' => $this->renters_uid_p,
		));
	}
	
	/**
	 * Loads the items that are associated with this rental
	 */
	public function loadRentedItems() {
		
		
		if($this->rental_type_p == 'Cottage') {
			$itemType = 'Cottage';	
		} else {
			$itemType = 'BoatItem';
		}
		
		$ids = array_keys(db_query('select t.rentable_item_id from ' . DBConf::$rented . ' as t where rental_start_time = ? and renters_uid = ?', array(
			$this->start_time_p,
			$this->renters_uid_p,
		))->execute()->fetchAllAssoc('rentable_item_id'));
		
		$this->rentedItems = array();
		
		foreach($ids as $id) {
			$this->rentedItems[$id] = new $itemType('standard', array('id' => $id));
		}
	}
	
	
	/**
	 * Loads the renters that are going on this trip
	 */
	public function loadRenters() {
		$ids = array_keys(db_query('select t.renter_id from ' . DBConf::$rented . ' as t where rental_start_time = ? and renters_uid = ?', array(
			$this->start_time_p,
			$this->renters_uid_p,
		))->execute()->fetchAllAssoc('renter_id'));
		
		$this->renterInstances = array();
		
		foreach($ids as $id) {
			$this->renterInstances[$id] = new Renter('standard', array(
				'id' => $id,
			));
		}
		
		
	}
	
	public function linkItem($itemID) {
		db_query('insert into ' . DBConf::$rented . '(rentable_item_id, rental_start_time, uid) values (?, ?, ?)', array(
			$itemID,
			$this->start_time_p,
			$this->renters_uid_p,
		));
	}
	
	public function linkRenter($renterID) {
		db_query('insert into ' . DBConf::$rentalRenters . '(renter_id, account_uid, rental_start_time, renters_uid) values (?, ? , ?, ?)', array(
			$renterID,
			$this->renters_uid_p,
			$this->start_time_p,
			$this->renters_uid_p,
		));
	}
	
	public function addCar($licence, $make, $colour) {
		db_query('insert into ' . DBConf::$rentersCar . '(liscence_plate, start_time, renter_uid, colour, make) values (?, ? , ?, ?, ?)', array(
			$licence,
			$this->start_time_p,
			$this->renters_uid_p,
			$colour,
			$make,
		));
	}
	
	public static function isReturningRental() {
		//TODO
		return false;
	}
}














