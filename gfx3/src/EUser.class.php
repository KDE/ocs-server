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
	
	public static $debug = true;
	
	private static $logged = false;
	private static $group = "anonymous";
	private static $pass;
	private static $nick;
	private static $mail;
	private static $id;
	
	public static function load(){
		
		EUser::$debug = true;
		EUser::$logged = false;
		EUser::$group = "anonymous";
		
		session_start();
		
		if(isset($_SESSION['nick'])){
			EUser::$logged = true;
			EUser::$nick = $_SESSION['nick'];
			EUser::$id = $_SESSION['id'];
			EUser::$group = $_SESSION['group'];
			EUser::$mail = $_SESSION['mail'];
			EUser::$pass = $_SESSION['pass'];
		} else {
			if(isset($_COOKIE['nick']) and isset($_COOKIE['pass'])){
				$r = EDatabase::q("SELECT * FROM ocs_person WHERE login='".$_COOKIE['nick']."' AND password='".$_COOKIE['pass']."' LIMIT 1");
				
				while($row = mysql_fetch_array($r)){
					EUser::$nick = $_SESSION['nick'] = $row['login'];
					EUser::$id = $_SESSION['id'] = $row['id'];
					EUser::$group = $_SESSION['group'] = $row['tgroup'];
					EUser::$mail = $_SESSION['mail'] = $row['email'];
					EUser::$pass = $_SESSION['pass'] = $row['password'];
					if(!empty($row['login'])){
						EUser::$logged = true;
					} else {
						EUser::$logged = false;
					}
				}
			
			} else {
				EUser::$logged = false;
			}
		}
		
	}
	
	
	public static function status(){
		return EUser::$status;
	}
	
	public static function password(){
		return EUser::$pass;
	}
	
	public static function mail(){
		return EUser::$mail;
	}
	
	public static function id(){
		return EUser::$id;
	}
	
	public static function nick(){
		return EUser::$nick;
	}
	
	public static function logged(){
		return EUser::$logged;
	}
	
	public static function login($nick, $pass){
		$r = EDatabase::q("SELECT * FROM ocs_person WHERE login='$nick' AND password='$pass' LIMIT 1");
		while($row = mysql_fetch_array($r)){
			
			if(!empty($row['login'])){
				EUser::$logged = true;
			} else {
				EUser::$logged = false;
			}
						
			setcookie("nick",$nick, time()+2419200);
			setcookie("pass",$pass, time()+2419200);
			EUser::$nick = $_SESSION['nick'] = $row['login'];
			EUser::$id = $_SESSION['id'] = $row['id'];
			EUser::$group = $_SESSION['group'] = $row['tgroup'];
			EUser::$logged = true;
			EUser::$mail = $_SESSION['mail'] = $row['email'];
			EUser::$pass = $_SESSION['pass'] = $row['password'];
		}
		return EUser::logged();
	}
	
	public static function logout(){
		session_destroy();
		setcookie("nick","", time()-2419200);
		setcookie("pass","", time()-2419200);
		EUser::$logged = false;
	}
	
	public static function gdeny($g){
		$groups = explode("|", EUser::$group);
		foreach($groups as $thGroup){
			if($thGroup==$g){
				ELog::error("You're not allowed to be here.");
				die($error);
			}
		}
		if(EUser::$logged==false){
			$error = "Loggati e riprova!";
			die($error);
		}
	}
	
	public static function refresh(){
		$r = EDatabase::q("SELECT * FROM ocs_person WHERE login='".EUser::$nick."' AND password='".EUser::$pass."' LIMIT 1");
		while($row = mysql_fetch_array($r)){
			EUser::$nick = $_SESSION['nick'] = $row['login'];
			EUser::$id = $_SESSION['id'] = $row['id'];
			EUser::$group = $_SESSION['group'] = $row['tgroup'];
			EUser::$logged = true;
			EUser::$mail = $_SESSION['mail'] = $row['email'];
		}
		return EUser::$logged();
	}
	
	public static function gallow($g){
		$allowedgroups = explode("|", $g);
		$groups = explode("|", EUser::$group);
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
	
	public static function belongs_to_group($g){
		$groups = explode("|", EUser::$group);
		foreach($groups as $thGroup){
			if($thGroup==$g){
				return true;
			}
		}
		//else return false :)
		return false;
	}
	
	public static function group(){
		return EUser::$group;
	}
	
	public static function register($nick, $pass, $group){
		EDatabase::q("INSERT INTO ocs_person (login, password, tgroup) VALUES ('$nick', '$pass', '$group')");
	}
	
}

?>
