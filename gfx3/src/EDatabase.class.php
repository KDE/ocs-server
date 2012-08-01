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

//db related include files
include_once("EDatasetter.class.php");
include_once("EData.class.php");

class EDatabase {
	
    //server config
	private static $db_name;
	private static $db_host;
	private static $db_user;
	private static $db_pass;
	
	private static $db_link = 0;
	
	private static $opened = false;
	private static $debug = true;
	
	private static $queries = 0;
	private static $status = 0;
	
	/**
	 * set temporary database information for the database class
	 * @param string $name
	 * @param string $host
	 * @param string $user
	 * @param string $pass
	 * @return null
	 */
	
	public static function load(){
		
		//loading vars from config file
		EDatabase::$db_name = EConfig::$data["database"]["name"];
		EDatabase::$db_host = EConfig::$data["database"]["host"];
		EDatabase::$db_user = EConfig::$data["database"]["user"];
		EDatabase::$db_pass = EConfig::$data["database"]["password"];
		
		//opening session
		$db = mysql_connect(EDatabase::$db_host, EDatabase::$db_user, EDatabase::$db_pass) or EDatabase::$status = 2;
		$db_select = mysql_select_db(EDatabase::$db_name, $db) or EDatabase::$status = 1;
		EDatabase::$db_link = $db;
		if(EDatabase::$status==0){
			EDatabase::$opened = true;
		}
		
	}
	
	public static function set_db_info($name,$host,$user,$pass){
		EDatabase::$db_name = $name;
		EDatabase::$db_host = $host;
		EDatabase::$db_user = $user;
		EDatabase::$db_pass = $pass;
	}
	
	public static function get_db_name(){ return EDatabase::$db_name; }
	public static function get_db_host(){ return EDatabase::$db_host; }
	public static function get_db_user(){ return EDatabase::$db_user; }
	public static function get_db_pass(){ return EDatabase::$db_pass; }
	
	/*
	 * This function is to assure that string or string array 
	 * are safe to be executed as parts of SQL queries
	 */
	public static function safe($s){
		if(is_array($s)){
			foreach($s as $key => $value){
				$s[$key] = mysql_real_escape_string($s[$key]);
			}
			return $s;
		} else {
			$s = mysql_real_escape_string($s);
			return $s;
		}
	}
	
	/**
	 * execute query on database
	 * @param string $q
	 * @return null
	 */
	public static function q($q){
		if(EDatabase::$opened==true){
			EDatabase::$queries += 1;
			$ret = mysql_query($q, EDatabase::$db_link);
			$error = mysql_error();
			if(empty($error)){ 
				$ret = $ret;
			} else {
				ELog::error($error."<br>Query string: ".$q);
			}
			return $ret;
		} else {
			if(EDatabase::$debug==false){
				ELog::error("sql session not already opened!");
			}
		}
	}
	
	/*TODO:	What is this method supposed to do?
	 *		Inspect.
	 */
	public static function sq($q){
		if(EDatabase::$opened==true){
			EDatabase::$queries += 1;
			$ret = mysql_query($q, EDatabase::$db_link); //FIXME: error management
			while($row=mysql_fetch_array($ret)){
				$number = $row[0];
			}
			return $number;
		} else {
			$error = " Query not executed due to mysql session not opened. Try to open one using open method. ";
			ELog::error($error);
		}
	}
	
	public static function table_exists($table){
		$r = EDatabase::q("SHOW TABLES LIKE '$table'");
		$row = mysql_fetch_row($r);
		if(empty($row)){
			return false;
		} else {
			return true;
		}
	}
	
	public static function num_rows($result){
		return mysql_num_rows($result);
	}
	
	public static function fetch_assoc($result){
		return mysql_fetch_assoc($result);
	}
	
	public static function status(){
		return EDatabase::$status;
	}
	
	public static function last_insert_id(){
		$r = EDatabase::sq("SELECT LAST_INSERT_ID()");
		return $r;
	}
	
	public static function all_queries(){
		return EDatabase::$queries;
	}
	
	public static function ping(){
		echo "EDatabase::ping() called!";
	}
	
	public static function unload(){
		if(EDatabase::$opened==true){
			// TODO: strange behaviour under root. Inspect.
			// mysql_close(EDatabase::db_link);
			EDatabase::$db_link = 0;
			EDatabase::$opened = false;
		} else {
			if(EDatabase::$debug==false){
				ELog::error("TRT GFX ISSUE: unable to close mysql session because no one was already opened.");
			}
		}
	}
}

?>
