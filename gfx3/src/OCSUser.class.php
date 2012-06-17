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
	private $login;
	private $password;
	private $firstname;
	private $lastname;
	private $email;
	
	private $main;
	private $persons;
	
	public function __construct(){
		//storing root object
		global $main;
		$this->main = $main;
		$this->persons = new EData("ocs_person");
	}
	
	public function checklogin($login,$passwd){
		$r = $this->persons->count("login", "login='$login' and password='$passwd'");
		if($r==0){
			return false;
		} else {
			return $login;
		}
	}
	
	public function user_exists($user){
		$r = $this->persons->is_there("login","login='$user'");
		return $r;
	}
	
	public function get_user_info($user){
		$user = $this->main->db->safe($user);
		if($this->user_exists($user)){
			$r = $this->main->db->q("select login,firstname,lastname,email from ocs_person where login='$user'");
			
			while($row=mysql_fetch_array($r)){
				$user_info["login"] = $row["login"];
				$user_info["firstname"] = $row["firstname"];
				$user_info["lastname"] = $row["lastname"];
				$user_info["email"] = $row["email"];
			}
			
			return $user_info;
		} else {
			return NULL;
		}
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
		//assure input is secure against injection
		$login = $this->main->db->safe($login);
		
		$persons = new EData("ocs_person");
		$r = $persons->count("login", "login='$login'");
		if($r==0){
			return false;
		} else {
			return true;
		}
	}
	
	public function countusersbyemail($email){
		$email = $this->main->db->safe($email);
		$persons = new EData("ocs_person");
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
		$login = $this->main->db->safe($login);
		$passwd = $this->main->db->safe($passwd);
		$firstname = $this->main->db->safe($firstname);
		$lastname = $this->main->db->safe($lastname);
		$email = $this->main->db->safe($email);
		
		$this->main->db->q("INSERT INTO ocs_person (login,password,firstname,lastname,email) VALUES ('$login','$passwd','$firstname','$lastname','$email')");
	}
	
}

?>
