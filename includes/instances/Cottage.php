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
		parent::__construct($type, $data);
		
		if($type != 'new') {
			//load the parent instance
			$this->rentalItem = new RentalItem('standard', array('id' => $this->id_p));
		}
	}
	
	/**
	 * Override to fetch the rental item from the database
	 * {@inheritDoc}
	 * @see DBTableInstance::getFromDB()
	 */
	public function getFromDB() {
		$result = parent::getFromDB();
		if($result) {
			$this->rentalItem = new RentalItem('standard', array('id' => $this->id_p));
		}
		return $result;
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
		$instances = parent::instanceLoadMultiple($data);
		
		$rentalItems = RentalItem::instanceLoadMultiple();
		$hashItems = array();
		//go through rental items and insert into hashmap for quick access
		foreach($rentalItems as $item) {
			$id = $item->getProperty('id');
			$hashItems[$id] = $item;
		}
			
		//go through the instances and assign rental items
		foreach($instances as $instance) {
			$id = $instance->getProperty('id');
			$instance->rentalItem = $hashItems[$id];
		}
		
		//return the loaded instances
		return $instances;
	}
	
	


}

