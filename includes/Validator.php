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
}