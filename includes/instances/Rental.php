<?php

module_load_include("php", "cpsc471prj", "includes/database/DBConf");
module_load_include("php", "cpsc471prj", "includes/database/DBTableInstance");
/**
 * Represents a DB tuple for a rental
 * @author andys
 *
 */
class Rental extends DBTableInstance {

	/**
	 * the unique id of a rental.
	 * @var unknown
	 */
	protected $rental_id_p;
	
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
	 * NOTE: SINCE RENTALS NOW HAVE AN INCREMENTING KEY,
	 * 		THIS FUNCTION SHOULD ONLY RETURN A SINGLE RENTED ITEM.
	 */
	public function loadRentedItems() {
		
		
		if($this->rental_type_p == 'Cottage') {
			$itemType = 'Cottage';	
		} else {
			$itemType = 'BoatItem';
		}
		
		$ids = array_keys(db_query('select t.rentable_item_id from ' . DBConf::$rented . ' as t where t.rental_id = ? and t.uid = ?', array(
			$this->rental_id_p,
			$this->renters_uid_p,
		))->fetchAllAssoc('rentable_item_id'));
		
		$this->rentedItems = array();
		
		foreach($ids as $id) {
			$this->rentedItems[$id] = new $itemType('standard', array('id' => $id));
		}
	}
	
	
	/**
	 * Loads the renters that are going on this trip
	 */
	public function loadRenters() {
		$ids = array_keys(db_query('select t.renter_id from ' . DBConf::$rented . ' as t where rental_id = ? and renters_uid = ?', array(
			$this->rental_id_p,
			$this->renters_uid_p,
		))->fetchAllAssoc('renter_id'));
		
		$this->renterInstances = array();
		
		foreach($ids as $id) {
			$this->renterInstances[$id] = new Renter('standard', array(
				'id' => $id,
			));
		}
		
		
	}
	
	public function linkItem($itemID) {
		db_query('insert into ' . DBConf::$rented . '(rentable_item_id, rental_id, uid) values (?, ?, ?)', array(
			$itemID,
			$this->rental_id_p,
			$this->renters_uid_p,
		));
	}
	
	public function linkRenter($renterID) {
		db_query('insert into ' . DBConf::$rentalRenters . '(renter_id, account_uid, rental_id, renters_uid) values (?, ? , ?, ?)', array(
			$renterID,
			$this->renters_uid_p,
			$this->rental_id_p,
			$this->renters_uid_p,
		));
	}
	
	public function addCar($licence, $make, $colour) {
		db_query('insert into ' . DBConf::$rentersCar . '(liscence_plate, rental_id, renter_uid, colour, make) values (?, ? , ?, ?, ?)', array(
			$licence,
			$this->rental_id_p,
			$this->renters_uid_p,
			$colour,
			$make,
		));
	}
	
	/**
	 * Checks if a rental is being booked by someone that has previosly booked a rental within the past year,
	 * (said rental must have already happened as well)
	 * 
	 * Intended For Cottages ONLY
	 * 
	 * @param int $start_time
	 * 		The currently requested start time for the rental
	 * @param int $uid
	 * 		The rental account uid creating the rental
	 */
	public static function isReturningRental($start_time, $uid) {
		$q = db_query('select r.start_time from rental as r where r.renters_uid = ? and r.start_time < ? and r.start_time > ? and r.rental_type = ?', array(
			$uid, $start_time, $start_time - 24 * 3600 * 365, 'Cottage',
		));
		
		return $q->fetchAssoc() !== false;
	}
	
	/**
	 * returns the price for this given rental.
	 * works on both cottages and boatItems.
	 */
	public function getPrice(){
		
		// NOTE - PROBABLY COULD HAVE DONE 80% OF THIS AS A HANDSOME QUERY, BUT
		// 	UNFORTUNATELY I WAS TOO MUCH OF A SCRUB. WAILLLL
		
		if($this->rental_type_p == 'Cottage') {

			$this->loadRentedItems();
			$priceTotal = 0;
			$item_id;
			if(count($this->rentedItems) == 1){
				foreach($this->rentedItems as $item){
					$item_id = $item->getProperty('id');
				}
				//find the season based on start time.
				$seasons = Season::instanceLoadMultiple();
				$final_season = array();
				foreach($seasons as $season){
					if($season->getProperty('start_time') <= $this->getProperty('start_time')
						&& ($season->getProperty('end_time') >= $this->getProperty('start_time'))){
						$final_season[] = $season;
					}
					
				}
				if(count($final_season == 1)){
					// assume that we can determine the cost guide based on cottage id and season start time
					$pricedFor = PricedFor::instanceLoadMultiple(array(
							'cottage_id' => $item_id,
							'season_start_time' => $final_season[0]->getProperty('start_time')
					));
					if(count($pricedFor) == 1){
						$guide = CottagePriceGuide::instanceLoadMultiple(array(
								'id' => $pricedFor[0]->getProperty('cost_guide_id')
						));
						
						// there MUST be only one guide. we really don't even have to check.
						if($this->getProperty('duration') <= 48){
							$priceTotal = $guide[0]->getProperty('two_day');
						}
						else if($this->getProperty('duration') <= 72){
							$priceTotal = $guide[0]->getProperty('three_day');
						}
						else{
							$priceTotal = $guide[0]->getProperty('week');
						}
						if($this->getProperty('returning') != 0){
							$priceTotal = $priceTotal * (100 - ($guide[0]->getProperty('rebook_discount')))/100;
						}
							return $priceTotal;
					}
					else{
						$message = 'There are too many valid price guides for the given cottage and season.';
						return 9999;
					}
			
				}
				else{
					$message = 'Too many valid seasons detected for rental time. Ambiguous cost.';
					return 9999;
				}
			}
			else{
				$message = 'There has been a terrible accident, multiple items linked to one rental.';
				return 9999;
			}
			
			return $priceTotal;
		} 
		else if($this->rental_type_p == 'BoatItem') {
			$this->loadRentedItems();
			$priceTotal = 0;
			foreach($this->rentedItems as $item){
				$test_start = $this->getProperty('start_time');
				
				$possibleRates = array();
				$possibleRates = BoatItemRate::instanceLoadMultiple(
						array('item_id' => $item->getProperty('id'),
							'duration' => $this->getProperty('duration')
				));
				if($possibleRates){
					$finalRate = array();
					foreach($possibleRates as $rate){
						if($rate->getProperty('start_time') < $this->getProperty('start_time')
								&& ($rate->getProperty('end_time') == null ||  $rate->getProperty('end_time') > $this->getProperty('start_time')))
							$finalRate[] = $rate;
					}
					if(count($finalRate) == 1){
						// there should be only one.
						$priceTotal = $finalRate[0]->getProperty('price');
					}
					else{
						$message = 'Could not find any rental rates for this item at specified time.';
						return 9999;
					}
				}
				else{
					$message = 'Could not find any possible rental rates for this item.';
					return 9999;
				}
				
				
			}
			return $priceTotal;
			
		}
		else{
			$message = 'Unidentified rental item type, cannot compute price.';
			return 9999;
		}
	}
}














