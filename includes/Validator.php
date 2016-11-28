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
	 * @param string $message
	 * 		Optional output parameter for the reason the cottage isn't availible
	 * @return bool
	 * 		true if the cottage is availible, false otherwise
	 */
	public static function isCottageAvailable($cottageID, $start_time, $duration, &$message = '') {
		$duration *= 3600;
		
		//fetch all other rentals on the provided cottage
		$rows = db_query('select r.start_time, r.duration from rented as d, rental as r where 
				d.rentable_item_id = ? and d.uid = r.renters_uid and d.rental_start_time = r.start_time', array(
					$cottageID,
				));
		
		$otherRentals = array();
		
		while($row = $rows->fetchAssoc()) {
			$otherRentals[] = $row;
		}
		
		
		//if the provided time range overlaps another rentals time range, flag an error
		foreach($otherRentals as $otherRental) {
			if($start_time + $duration > $otherRental['start_time'] && $start_time < $otherRental['start_time'] + $otherRental['duration'] * 3600) {
				$message = 'That time slot has already been booked, please select another time';
				return false;
			}
		}
		
		$seasons = array();
		
		//now we need to check that the provided range falls within a price guide for the season
		$rows = db_query('select s.start_time, s.end_time from season as s, priced_for as p where p.cottage_id = ? and p.season_start_time = s.start_time', array($cottageID));
		
		while($row = $rows->fetchAssoc()) {
			$seasons[] = $row;
		}
		
		//check that the start time falls within a season
		foreach($seasons as $season) {
			if($start_time >= $season['start_time'] && $start_time <= $season['end_time']) {
				
				return true;
			}
		}
		
		$message = 'No booking rates have been set for the selected time, the resort is not open';
		
		return false;

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