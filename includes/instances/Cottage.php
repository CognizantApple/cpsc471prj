<?php
module_load_include('php', 'cpsc471prj', 'includes/instances/RentalItem');

/**
 * Represents a cottage tuple from the database
 * @author Patrick
 *
 */
class Cottage extends DBTableInstance{

	/**
	 * The rental Items ID
	 * @var int
	 */
	protected $id_p;

	/**
	 * Max number of adults in the cottage
	 * @var int
	 */
	protected $max_adults_p;
	
	/**
	 * The base # of adults in the cottage
	 * @var int
	 */
	protected $base_adults_p;

	/**
	 * This cottage's number
	 * @var int
	 */
	protected $number_p;
	
	/**
	 * What type of cottage is this?
	 * @var string
	 */
	protected $class_name_p;
	
	/**
	 * This cottages entry in the rental item table
	 * @var RentalItem
	 */
	protected $rentalItem;

	/**
	 * Calls parent ctor after setting table
	 */
	public function __construct($type = 'new', $data = array()){
		$this->tableName = DBConf::$cottage;
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
		$toSelectCottage = $this->getPersistentNotNull();
		
		$toSelectRentalItem = $this->rentalItem->getPersistentNotNull();
		
		if(count($toSelectCottage) == 0 && count($toSelectRentalItem) == 0) {
			throw new PDOException("No member variables have been set");
		}
		
		//pass by reference to fill
		$arguments = array();
		$sql = $this->joinOnRentalItem($toSelectCottage, $toSelectRentalItem, $arguments);
		
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
		
		$toSelectCottage = $data;
		$toSelectRentalItem = $data;
		$dataKeys = array_keys($data);
		//we need to sort the data into the right arrays,
		//start by looking at the members of calling class
		$sampleC = new Cottage('new');
		$cottageMembers = $sampleC->getPersistentVars();
		$sampleR = new RentalItem('new');
		$rentalItemMembers = $sampleR->getPersistentVars();
		
		
		//go through all the provided keys to select on,
		//and unset anything that doesn't belong to that table
		foreach($dataKeys as $member) {
			if(!isset($cottageMembers[$member])) {
				unset($toSelectCottage[$member]);
			}
			
			if(!isset($rentalItemMembers[$member])) {
				unset($toSelectRentalItem[$member]);
			}
		}
		
		$arguments = array();
		
		$sql = $sampleC->joinOnRentalItem($toSelectCottage, $toSelectRentalItem, $arguments);
		
		//execute the query
		$result = db_query($sql, $arguments);
		
		$instances = array();
		
		//create an instance from each result
		while($row = $result->fetchAssoc()) {
			$instance = new Cottage('new');
			$instance->setCopy($row);
			$instance->rentalItem->setCopy($row);
			$instances[] = $instance;
		}
		
		//return the loaded instances
		return $instances;
	}
	
	/**
	 * Instead of a plain select, we can execute a join on cottage and rental
	 * {@inheritDoc}
	 * @see DBTableInstance::selectQuery()
	 */
	protected function joinOnRentalItem($toSelectCottage, $toSelectRentalItem, &$arguments) {
		$sql = 'SELECT * FROM ' . $this->tableName . ' AS C, ' . DBConf::$rentableItem. ' AS R WHERE C.id = R.id ';
		
		$arguments = array();
		
		foreach($toSelectCottage as $index => $value) {
			
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

