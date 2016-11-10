<?php
/**
 * Class for checking user access
 * Corresponds to the Rental_Account
 * @author Patrick
 *
 */
class User {
	
	/**
	 * Does nothing
	 */
	public function User() {}
	
	/**
	 * Adds the new data to the user. Assumes $NewUserData has the proper variables
	 * Assumes that the information has been checked for validity
	 */
	public function AddNewUser($NewUserData){
		
	}
	
	/**
	 * Get the current user's UID
	 * @throws Exception
	 * 		If no user is logged in
	 */
	public function getUID() {	
		if(!user_is_logged_in()) {
			throw new Exception("User not logged in");
		}
		
		global $user;
		
		return $user->uid;
	}
	
	/**
	 * Get the current users Email address
	 * @throws Exception
	 * 		If no user is logged in
	 */
	public function getEmail() {
		if(!user_is_logged_in()) {
			throw new Exception("User not logged in");
		}
		
		global $user;
		
		return $user->email;
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
		
		return user_has_role(array_search($roleName, $roles));
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
	
	public static function isCreditCardValid($numberInQuestion){
		return is_numeric($numberInQuestion);
	}
	
	public static function isPhoneNumberValid($numberInQuestion){
		$numberInQuestion = str_replace(array('-', '(', ')', ' '), '', $numberInQuestion);
		if(is_numeric($numberInQuestion)){
			
			$lengthOfString = strlen($numberInQuestion);
			if($lengthOfString == 9 || $lengthOfString == 10){
				
				return true;
			}
		}
		return false;
	}
}
