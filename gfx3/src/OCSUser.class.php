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
	
	private $logged = false; //some kind of log
	private $id;
	
	private $main;
	private $persons;
	
	public function __construct(){
		//storing root object
		global $main;
		$this->main = $main;
		$this->persons = new EData("ocs_person");
	}
	
	/*
	 * getters/setters
	 */
	public function id(){
		return $this->id;
	}
	
	public function is_logged(){
		return $this->logged;
	}
	
	/*
	 * attempt an authentication thorugh nickname:password
	 * and populates object data if successfull
	 */
	public function checklogin($login,$passwd){
		$r = $this->persons->count("login", "login='$login' and password='$passwd'");
		if($r==0){
			$this->logged = false;
			return false;
		} else {
			$this->logged = true;
			
			$data = $this->persons->find("*","where login='$login' and password='$passwd'");
			$this->id = $data[0]["id"];
			$this->login = $data[0]["login"];
			$this->firstname = $data[0]["firstname"];
			$this->lastname = $data[0]["lastname"];
			$this->email = $data[0]["email"];
			
			return $login;
		}
	}
	
	/*
	 * Some utils functions regarding users
	 */
	
	public function exists($user){
		$user = $this->main->db->safe($user);
		$r = $this->persons->is_there("login","login='$user'");
		return $r;
	}
	
	public function get_user_info(){
		$user_info["id"] = $this->id;
		$user_info["login"] = $this->login;
		$user_info["firstname"] = $this->firstname;
		$user_info["lastname"] = $this->lastname;
		$user_info["email"] = $this->email;
		
		return $user_info;
	}
	
	public function register($login,$passwd,$firstname,$lastname,$email){
		$login = $this->main->db->safe($login);
		$passwd = $this->main->db->safe($passwd);
		$firstname = $this->main->db->safe($firstname);
		$lastname = $this->main->db->safe($lastname);
		$email = $this->main->db->safe($email);
		
		$this->main->db->q("INSERT INTO ocs_person (login,password,firstname,lastname,email) VALUES ('$login','$passwd','$firstname','$lastname','$email')");
	}
	
	/*
	 * TODO: utils function which are semantically in the wrong place. Inspect and fix.
	 */
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
	
	public function countusersbyemail($email){
		$email = $this->main->db->safe($email);
		$persons = new EData("ocs_person");
		$r = $persons->count("login", "email='$email'");
		return $r;
	}
	
	/*
	 * Obscure magic string told me by the elders.
	 * If modified, I don't guarantee you'll be safe anymore.
	 */
	public function isvalidemail($email){
		if(preg_match("/^([\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+\.)*[\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+@((((([a-z0-9]{1}[a-z0-9\-]{0,62}[a-z0-9]{1})|[a-z])\.)+[a-z]{2,6})|(\d{1,3}\.){3}\d{1,3}(\:\d{1,5})?)$/i", $email)){
			return true;
		} else {
			return false;
		}
	}
	
}

?>
