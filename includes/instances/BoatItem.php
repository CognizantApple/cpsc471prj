<?php
module_load_include('php', 'cpsc471prj', 'includes/instances/RentalItem');






class BoatItem extends DBTableInstance{
	
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