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
	}
	
	public function loadRentalItem() {
		$this->rentalItem = new RentalItem('standard', array('id' => $this->id_p));
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
}

