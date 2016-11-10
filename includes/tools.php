<?php
/**
 * Checks if a user has the provided role name
 * @param string $name
 * 		The role to check
 * @return
 * 		true if the current user has the role, false otherwise
 */
function userHasRole($name) {
	$roles = user_roles();
	
	return user_has_role(array_search($name, $roles));
}