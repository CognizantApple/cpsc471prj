<?php
module_load_include("php", "cpsc471prj", "includes/database/DBTableInstance");

/**
 * Class for checking user access
 * Corresponds to the Rental_Account
 * @author Patrick
 *
 */
class User extends DBTableInstance {
	
	protected $uid_p;
	protected $name_p;
	protected $mail_p;
	protected $pass_p;
	
	
	/**
	 * Does nothing
	 */
	public function __construct($type = 'current', $data = array()) {
		$this->tableName = 'users';
		
		if($type == 'standard') {
			parent::__construct('standard', $data);
			return;
		}
		
		
		if(user_is_logged_in()) {
			global $user;
			parent::__construct('standard', array('uid' => $user->uid));
		} else {
			parent::__construct('new', array());
		}
		
	}
	
	/**
	 * Creates a new drupal user from the provided info
	 */
	public function create(){
		$newUserForDrupal = array(
			'name' => $this->name_p,
			'pass' => $this->pass_p,
			'mail' => $this->mail_p,
			'status' => 1,
			'init' => $this->mail_p,
			'is_new' => true,
		);
		
		user_save('', $newUserForDrupal);
		$this->pass_p = null; //it will have been hashed
		$this->uid_p = null;
		$this->getFromDB();//reload self
	}
	
	/**
	 * Checks if there is a user currently logged in
	 * @return bool
	 * 		true if the current user is logged in,
	 * 		false otherwise
	 */
	public function loggedIn() {
		return $this->uid_p !== null;
	}
	
	/**
	 * Logs the currently set  $name into drupal
	 */
	public function login() {	
		$userTemp = user_load($this->uid_p);
		if($userTemp !== false) {
			global $user;
			$user = $userTemp;
			user_login_finalize();
		} 	
	}
		
	/**
	 * Checks if a user has the provided role name
	 *
	 * Roles are
	 * Renter, Manager
	 *
	 * @param string $roleName
	 * 		The role to check
	 * @return
	 * 		true if the current user has the role, false otherwise
	 */
	public function hasRole($roleName) {
		$roles = user_roles();
		
		global $user;
		
		return user_has_role(array_search($roleName, $roles), $user);
	}
	
	public function setRole($roleName, $hasRole = true) {
		global $user;
		
		$roles = $user->roles;
		
		$rid = array_search($roleName, user_roles());
		
		if($hasRole) {
			$roles[$rid] = $roleName;
		} else {
			unset($roles[$rid]);
		}
		
		user_save($user, array('roles' => $roles));	
	}
	
	/**
	 * Checks if a user has the provided permission
	 *
	 * Permission are
	 * renter, manager
	 *
	 * @param string $permissionName
	 * 		The permission to check for
	 * @return Boolean
	 * 		true if the user has the permission, false otherwise
	 */
	public function hasPermission($permissionName) {
		return user_access($permissionName);
	}
}
