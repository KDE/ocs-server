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

class OCS_User{
	
	//ocs mandatory user attributes
	private $login;
	private $password;
	private $firstname;
	private $lastname;
	private $email;
	
	public function __construct(){
		//
	}
	
	//TODO: ask for more infos about password validation
	public function isvalidpassword($pass){
		if(strlen($pass)>=8){
			return true;
		} else {
			return false;
		}
	}
	
	public function isloginname($login){
		if(preg_match("([A-Za-z0-9]*)",$login)){
			return true;
		} else {
			return false;
		}
	}
	
	public function exists($login){
		$login = $edb->safe($login);
		
		$persons = new EData("ocs_person",$this->main);
		$r = $persons->count("login", "login='$login'");
		if($r==0){
			return false;
		} else {
			return true;
		}
	}
	
	public function countusersbyemail($email){
		$email = $edb->safe($email);
		$persons = new EData("ocs_person",$this->main);
		$r = $persons->count("login", "email='$email'");
		return $r;
	}
	
	public function isvalidemail($email){
		if(preg_match("/^([\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+\.)*[\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+@((((([a-z0-9]{1}[a-z0-9\-]{0,62}[a-z0-9]{1})|[a-z])\.)+[a-z]{2,6})|(\d{1,3}\.){3}\d{1,3}(\:\d{1,5})?)$/i", $email)){
			return true;
		} else {
			return false;
		}
	}
	
	public function register($login,$passwd,$firstname,$lastname,$email){
		$login = $edb->safe($login);
		$passwd = $edb->safe($passwd);
		$firstname = $edb->safe($firstname);
		$lastname = $edb->safe($lastname);
		$email = $edb->safe($email);
		
		$edb->q("INSERT INTO ocs_person (login,password,firstname,lastname,email) VALUES ('$login','$passwd','$firstname','$lastname','$email')");
	}
	
}

?>
