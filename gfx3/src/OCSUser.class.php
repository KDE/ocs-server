<?php

/*
 *   TRT GFX 3.0.1 (beta build) BackToSlash
 * 
 *   support:	happy.snizzo@gmail.com
 *   website:	http://trt-gfx.googlecode.com
 *   credits:	Claudio Desideri
 *   
 *   This software is released under the MIT License.
 *   http://opensource.org/licenses/mit-license.php
 */ 

class OCSUser{
	
	//ocs mandatory user attributes
	private static  $login;
	private static  $password;
	private static  $firstname;
	private static  $lastname;
	private static  $email;
	
	private static  $logged = false; //some kind of log
	private static  $id;
	
	private static  $persons;
	
	public static  function load(){
		//storing root object
		OCSUser::$persons = new EData("ocs_person");
	}
	
	/*
	 * getters/setters
	 */
	public static  function id(){
		return OCSUser::$id;
	}
	
	public static  function is_logged(){
		return OCSUser::$logged;
	}
	
	/*
	 * attempt an authentication thorugh nickname:password
	 * and populates object data if successfull
	 */
	public static  function checklogin($login,$passwd){
		$r = OCSUser::$persons->count("login", "login='$login' and password='$passwd'");
		if($r==0){
			OCSUser::$logged = false;
			return false;
		} else {
			OCSUser::$logged = true;
			
			$data = OCSUser::$persons->find("*","where login='$login' and password='$passwd'");
			OCSUser::$id = $data[0]["id"];
			OCSUser::$login = $data[0]["login"];
			OCSUser::$firstname = $data[0]["firstname"];
			OCSUser::$lastname = $data[0]["lastname"];
			OCSUser::$email = $data[0]["email"];
			
			return $login;
		}
	}
	
	/*
	 * Some utils functions regarding users
	 */
	
	public static  function exists($user){
		$user = EDatabase::safe($user);
		$r = OCSUser::$persons->is_there("login","login='$user'");
		return $r;
	}
	
	public static  function get_user_info($username=""){
		if($username==OCSUser::$login){
			$user_info["id"] = OCSUser::$id;
			$user_info["login"] = OCSUser::$login;
			$user_info["firstname"] = OCSUser::$firstname;
			$user_info["lastname"] = OCSUser::$lastname;
			$user_info["email"] = OCSUser::$email;
			
			return $user_info;
		} else {
			$ocs_person = new EData("ocs_person");
			$user_info = $ocs_person->find("*","where login='$username' limit 1");
			return $user_info;
		}
	}
	
	public static  function register($login,$passwd,$firstname,$lastname,$email){
		$login = EDatabase::safe($login);
		$passwd = EDatabase::safe($passwd);
		$firstname = EDatabase::safe($firstname);
		$lastname = EDatabase::safe($lastname);
		$email = EDatabase::safe($email);
		
		EDatabase::q("INSERT INTO ocs_person (login,password,firstname,lastname,email) VALUES ('$login','$passwd','$firstname','$lastname','$email')");
	}
	
	/*
	 * TODO: utils function which are semantically in the wrong place. Inspect and fix.
	 */
	//TODO: ask for more infos about password validation
	public static  function isvalidpassword($pass){
		if(strlen($pass)>=8){
			return true;
		} else {
			return false;
		}
	}
	
	public static  function isloginname($login){
		if(preg_match("([A-Za-z0-9]*)",$login)){
			return true;
		} else {
			return false;
		}
	}
	
	public static  function countusersbyemail($email){
		$email = EDatabase::safe($email);
		$persons = new EData("ocs_person");
		$r = $persons->count("login", "email='$email'");
		return $r;
	}
	
	/*
	 * Obscure magic string told me by the elders.
	 * If modified, I don't guarantee you'll be safe anymore.
	 */
	public static  function isvalidemail($email){
		if(preg_match("/^([\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+\.)*[\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+@((((([a-z0-9]{1}[a-z0-9\-]{0,62}[a-z0-9]{1})|[a-z])\.)+[a-z]{2,6})|(\d{1,3}\.){3}\d{1,3}(\:\d{1,5})?)$/i", $email)){
			return true;
		} else {
			return false;
		}
	}
	
}

?>
