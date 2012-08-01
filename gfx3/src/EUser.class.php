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

class EUser{
	
	public $debug = true;
	
	private $logged = false;
	private $group = "anonymous";
	private $pass;
	private $nick;
	private $mail;
	private $id;
	
	private $status;
	
	public function __construct(){
		
		session_start();
		if(isset($_SESSION['nick'])){
			$this->logged = true;
			$this->nick = $_SESSION['nick'];
			$this->id = $_SESSION['id'];
			$this->group = $_SESSION['group'];
			$this->mail = $_SESSION['mail'];
			$this->pass = $_SESSION['pass'];
		} else {
			if(isset($_COOKIE['nick']) and isset($_COOKIE['pass'])){
				$r = EDatabase::q("SELECT * FROM users WHERE nick='".$_COOKIE['nick']."' AND pass='".$_COOKIE['pass']."' LIMIT 1");
				//error management in case user is not installed
				if(!$r or EDatabase::status()!=0){
					//catching an error, setting status and doing nothing
					$this->status = 1;
				} else {
					//proceding with a fine status
					$this->status = 0;
					while($row = mysql_fetch_array($r)){
						$this->nick = $_SESSION['nick'] = $row['nick'];
						$this->id = $_SESSION['id'] = $row['id'];
						$this->logged = true;
						$this->group = $_SESSION['group'] = $row['tgroup'];
						$this->mail = $_SESSION['mail'] = $row['mail'];
						$this->pass = $_SESSION['pass'] = $row['pass'];
					}
				}
			} else {
				$this->logged = false;
			}
		}
	}
	
	//get current userclass installation status
	public function getStatus(){
		$r = EDatabase::q("SELECT * FROM users LIMIT 1");
		if(!$r or EDatabase::status()!=0){
			//catching an error, setting status
			$this->status = 1;
			ELog::warning("EUser is not logged!");
		}
	}
	
	
	public function status(){
		return $this->status;
	}
	
	public function nick(){
		return $this->nick;
	}
	
	public function mail(){
		return $this->mail;
	}
	
	public function id(){
		return $this->id;
	}
	
	public function logged(){
		return $this->logged;
	}
	
	public function login($nick, $pass){
		$r = EDatabase::q("SELECT * FROM users WHERE nick='$nick' AND pass='$pass' LIMIT 1");
		while($row = mysql_fetch_array($r)){
			setcookie("nick",$nick, time()+2419200);
			setcookie("pass",$pass, time()+2419200);
			$this->nick = $_SESSION['nick'] = $row['nick'];
			$this->id = $_SESSION['id'] = $row['id'];
			$this->group = $_SESSION['group'] = $row['tgroup'];
			$this->logged = true;
			$this->mail = $_SESSION['mail'] = $row['mail'];
			$this->pass = $_SESSION['pass'] = $row['pass'];
		}
		return $this->logged();
	}
	
	public function logout(){
		session_destroy();
		setcookie("nick","", time()-2419200);
		setcookie("pass","", time()-2419200);
		$this->logged = false;
	}
	
	public function gdeny($g){
		$groups = explode("|", $this->group);
		foreach($groups as $thGroup){
			if($thGroup==$g){
				ELog::error("You're not allowed to be here.");
				die($error);
			}
		}
		if($this->logged==false){
			$error = "Loggati e riprova!";
			die($error);
		}
	}
	
	public function refresh(){
		$r = EDatabase::q("SELECT * FROM users WHERE nick='".$this->nick."' AND pass='".$this->pass."' LIMIT 1");
		while($row = mysql_fetch_array($r)){
			$this->nick = $_SESSION['nick'] = $row['nick'];
			$this->id = $_SESSION['id'] = $row['id'];
			$this->group = $_SESSION['group'] = $row['tgroup'];
			$this->logged = true;
			$this->mail = $_SESSION['mail'] = $row['mail'];
		}
		return $this->logged();
	}
	
	public function gallow($g){
		$allowedgroups = explode("|", $g);
		$groups = explode("|", $this->group);
		foreach($groups as $thGroup){
			foreach($allowedgroups as $alGroup){
				if($thGroup==$alGroup){
					return true;
				}
			}
		}
		ELog::error("You're not allowed to be here.");
		return false;
	}
	
	public function belongs_to_group($g){
		$groups = explode("|", $this->group);
		foreach($groups as $thGroup){
			if($thGroup==$g){
				return true;
			}
		}
		//else return false :)
		return false;
	}
	
	public function group(){
		return $this->group;
	}
	
	public function register($nick, $pass, $group){
		EDatabase::q("INSERT INTO users (nick, pass, tgroup) VALUES ('$nick', '$pass', '$group')");
	}
	
}

?>
