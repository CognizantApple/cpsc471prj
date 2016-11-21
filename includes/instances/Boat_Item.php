<?php
module_load_include('php', 'cpsc471prj', 'includes/instances/RentalItem');






class Boat_Item extends DBTableInstance{
	
	/**
	 * The rental Items ID
	 * @var int
	 */
	protected $id_p;
	
	/**
	 * The type of boat rental item (Boat, life jacket, etc.)
	 * @var string
	 */
	protected $type;
	
	/**
	 * The name of the item
	 * @var string
	 */
	protected $name;
	
	/**
	 * How many people may use the item at one time
	 * @var int
	 */
	protected $capacity;
	
	/**
	 * This boat items entry in the Rental Item table
	 * @var RentalItem
	 */
	protected $rentalItem;
	
	
	public function __construct($type = 'new', $data = array()){
		$this->tableName = DBConf::$boatRentalItem;
		$this->rentalItem = new RentalItem('new');
	
		parent::__construct($type, $data);
	}
	
	
	/**
	 * Override to fetch the rental item from the database
	 * {@inheritDoc}
	 * @see DBTableInstance::getFromDB()
	 */
	public function getFromDB() {
		//get the set persistent members
		$toSelectBoatItem = $this->getPersistentNotNull();
	
		$toSelectRentalItem = $this->rentalItem->getPersistentNotNull();
	
		if(count($toSelectBoatItem) == 0 && count($toSelectRentalItem) == 0) {
			throw new PDOException("No member variables have been set");
		}
	
		//pass by reference to fill
		$arguments = array();
		$sql = $this->joinOnRentalItem($toSelectBoatItem, $toSelectRentalItem, $arguments);
	
		//execute the query and fetch some row from the result
		$result = db_query($sql, $arguments);
	
		$row = $result->fetchAssoc();
	
		if($row === false) {
			return false;
		}
		//if there was a result, set ourselves to a copy of it
		$this->setCopy($row);
		$this->rentalItem->setCopy($row);
	
		return true;
	}
	
	/**
	 * Override to create a new rental item
	 * {@inheritDoc}
	 * @see DBTableInstance::storeToDB()
	 */
	public function storeToDB() {
		$this->rentalItem = new RentalItem('new');
		$this->rentalItem->setProperty('is_active', 1);
		$this->rentalItem->storeToDB(); //multiple thing can be active, so we don't call create
	
		//we need to select the max rental item ID now
		$sql = 'SELECT MAX(id) FROM ' . DBConf::$rentableItem;
		$result = db_query($sql)->fetchAssoc();
	
		$this->id_p = $result['MAX(id)'];
	
		parent::storeToDB();
	}
	
	/**
	 * Override to update the rental item instance
	 * {@inheritDoc}
	 * @see DBTableInstance::updateInDB($toUpdate)
	 */
	public function updateInDB($toUpdate) {
		parent::updateInDB($toUpdate);
	
		if(isset($toUpdate['id'])) {
			//this one will update id if required
			$this->rentalItem->updateInDB($toUpdate);
		}
	}
	
	/**
	 * Override to load the parent rental item
	 * @param array $data
	 * 		List of items to select on
	 * @return Cottage[]
	 * 		The selected cottage instances
	 */
	public static function instanceLoadMultiple($data = array()) {
	
		$toSelectBoatItem = $data;
		$toSelectRentalItem = $data;
		$dataKeys = array_keys($data);
		//we need to sort the data into the right arrays,
		//start by looking at the members of calling class
		$sampleC = new BoatItem('new');
		$BoatItemMembers = $sampleC->getPersistentVars();
		$sampleR = new RentalItem('new');
		$rentalItemMembers = $sampleR->getPersistentVars();
	
	
		//go through all the provided keys to select on,
		//and unset anything that doesn't belong to that table
		foreach($dataKeys as $member) {
			if(!isset($BoatItemMembers[$member])) {
				unset($toSelectBoatItem[$member]);
			}
				
			if(!isset($rentalItemMembers[$member])) {
				unset($toSelectRentalItem[$member]);
			}
		}
	
		$arguments = array();
	
		$sql = $sampleC->joinOnRentalItem($toSelectBoatItem, $toSelectRentalItem, $arguments);
	
		//execute the query
		$result = db_query($sql, $arguments);
	
		$instances = array();
	
		//create an instance from each result
		while($row = $result->fetchAssoc()) {
			$instance = new Boat_Item('new');
			$instance->setCopy($row);
			$instance->rentalItem->setCopy($row);
			$instances[] = $instance;
		}
	
		//return the loaded instances
		return $instances;
	}
	
	/**
	 * Creates a join-select queury between cottage and rental,
	 *
	 * this is a pseudo-override of selectQuery
	 *
	 * @param array $toSelectCottage
	 * 		An array of columns to select on the cottage table (indexed by column name)
	 * @param array $toSelectRentalItem
	 * 		An array of columns to select on the rental_item table
	 * @param array $arguments
	 * 		An output array of arguments to db_query
	 * @return string
	 * 		The SQL query to execute
	 */
	protected function joinOnRentalItem($toSelectBoatItem, $toSelectRentalItem, &$arguments = array()) {
		$sql = 'SELECT * FROM ' . $this->tableName . ' AS C, ' . DBConf::$rentableItem. ' AS R WHERE C.id = R.id ';
	
		$arguments = array();
	
		foreach($toSelectBoatItem as $index => $value) {
				
			$sql .= ' AND ';
			$sql .=  'C.' . $index . '= ?';
			$arguments[] = $value;
		}
	
		foreach($toSelectRentalItem as $index => $value) {
			$sql .= ' AND ';
			$sql .=  'R.' . $index . '= ?';
			$arguments[] = $value;
		}
	
		return $sql;
	}
	
	
}