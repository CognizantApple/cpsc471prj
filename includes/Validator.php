<?php
/**
 * Class of static methods for validating input formates
 * @author Patrick
 */
class Validator {
	/**
	 * Checks if a credit card number is valid
	 * 
	 * Note: this just checks that a credit card number is made of numbers for now
	 * 
	 * @param string | int $creditCardNumber
	 * 		The credit card number to check
	 * @return bool
	 * 		true if the number is valid, false otherwise
	 */
	public static function isCreditCardValid($creditCardNumber){
		return is_numeric($creditCardNumber);
	}
	
	/**
	 * Checks if a phone number is valid
	 * @param string | int $phoneNumber
	 * 		The phone number to check
	 * @return bool
	 * 		true if the number is valid, false otherwise
	 */
	public static function isPhoneNumberValid($phoneNumber){
		//replace -, (, ) and ' ', which people put in strings for phone numbers often
		$phoneNumber = str_replace(array('-', '(', ')', ' '), '', $phoneNumber);
		//check that only numbers remain
		if(!is_numeric($phoneNumber)){
			return false;
		}
		//check that the string is long enough
		$length = strlen($phoneNumber);
		if($length != 9 && $length != 10){
			return false;
		}
		
		return true;
	}
	
	/**
	 * Checks that an email is valid
	 * @param string $email
	 * 		The email to check
	 * @return bool
	 * 		true if the email is valid, false otherwise	
	 */
	public static function isEmailValid($email){
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}
	
	/**
	 * Checks that a string represents a positive integer
	 * @param string $str
	 * 		The string to check
	 * @return bool
	 * 		true if the string matches, false otherwise
	 * 		
	 */
	public static function isPositive($str) {
		if(!is_numeric($str)) {
			return false;
		}
		
		return intval($str) >= 0;
	}
	
	/**
	 * Checks if a cottage is availible to be rented
	 * 
	 * @param int $cottageID
	 * 		The primary key of a cottage in the db
	 * @param int $start_time
	 * 		A unix timestamp for when the request starts
	 * @param int $duration
	 * 		A duration of rental (In number of days)
	 * @return bool
	 * 		true if the cottage is availible, false otherwise
	 */
	public static function isCottageAvailable($cottageID, $start_time, $duration) {
		return true;

	}
	
	/**
	 * Checks if a boat is availible to be rented
	 *
	 * @param int $itemID
	 * 		The primary key of a BoatItem in the db
	 * @param int $start_time
	 * 		A unix timestamp for the day of the rental
	 * @param int $duration
	 * 		A duration of rental (In number of hours)
	 * @return bool
	 * 		true if the Boat item is availible, false otherwise
	 */
	public static function isBoatItemAvailable($itemID, $start_time, $duration) {
		return true;
	
	}
	
	public static function isName($str) {
		return ctype_alpha($str);
	}
	
	/**
	 * Validates a time as hh:mm (24h time)
	 * 
	 * @param string $str
	 * 		The string to check
	 * @return int | bool
	 * 		The number of seconds into the day if the format matches, or false if the format is invalid
	 */
	public static function isHHMM($str) {
		if(preg_match('/(1[012]|0[0-9]):([0-5][0-9])/', $str)) {
			$parts = explode(':', $str);
			
			$hours = intval($parts[0]);
			$minutes = intval($parts[1]);
			return 3600 * $hours + 60 * $minutes;
		} else {
			return false;
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}