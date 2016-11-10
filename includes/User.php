<?php
/**
 * Class for checking user access
 * @author Patrick
 *
 */
class User {
	
	/**
	 * Does nothing
	 */
	public function User() {}
	
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
		
		global $user;
		
		return user_has_role(array_search($roleName, $roles), $user);
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
