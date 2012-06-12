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
	private $db_name = "prova";
	private $db_host = "localhost";
	private $db_user = "root";
	private $db_pass = "asd";
	
	private $db_link = 0;
	private $main;
	
	private $opened = false;
	private $debug = true;
	
	private $queries = 0;
	private $status = 0;
	
	/**
	 * set temporary database information for the database class
	 * @param string $name
	 * @param string $host
	 * @param string $user
	 * @param string $pass
	 * @return null
	 */
	
	public function __construct(){
		//opening session
		$db = mysql_connect($this->db_host, $this->db_user, $this->db_pass) or $this->status = 2;
		$db_select = mysql_select_db($this->db_name, $db) or $this->status = 1;
		$this->db_link = $db;
		if($this->status==0){
			$this->opened = true;
		}
	}
	
	public function set_db_info($name,$host,$user,$pass){
		$this->db_name = $name;
		$this->db_host = $host;
		$this->db_user = $user;
		$this->db_pass = $pass;
	}
	
	public function get_db_name(){ return $this->db_name; }
	public function get_db_host(){ return $this->db_host; }
	public function get_db_user(){ return $this->db_user; }
	public function get_db_pass(){ return $this->db_pass; }
	
	
	public function safe($s){
		$s = mysql_real_escape_string($s);
		return $s;
	}
	
	/**
	 * execute query on database
	 * @param string $q
	 * @return null
	 */
	public function q($q){
		if($this->opened==true){
			$this->queries += 1;
			$ret = mysql_query($q, $this->db_link);
			$error = mysql_error();
			if(empty($error)){ 
				$ret = $ret;
			} else {
				$elog->error($error."<br>Query string: ".$q);
			}
			return $ret;
		} else {
			if($this->debug==false){
				$elog->error("sql session not already opened!");
			}
		}
	}
	
	public function sq($q){
		if($this->opened==true){
			$this->queries += 1;
			$ret = mysql_query($q, $this->db_link); //FIXME: error management
			while($row=mysql_fetch_array($ret)){
				$number = $row[0];
			}
			return $number;
		} else {
			$error = " Query not executed due to mysql session not opened. Try to open one using open method. ";
			$elog->error($error);
		}
	}
	
	
	public function num_rows($result){
		return mysql_num_rows($result);
	}
	
	public function fetch_assoc($result){
		return mysql_fetch_assoc($result);
	}
	
	public function status(){
		return $this->status;
	}
	
	//function not related to databases but useful when needed to test if the object exists
	public function is_alive(){
		return true;
	}
	
	public function all_queries(){
		return $this->queries;
	}
	
	public function ping(){
		echo "EDatabase::ping() called!";
	}
	
	public function __destruct(){
		if($this->opened==true){
			mysql_close();
			$this->db_link = 0;
			$this->opened = false;
		} else {
			if($this->debug==false){
				$elog->error("TRT GFX ISSUE: unable to close mysql session because no one was already opened.");
			}
		}
	}
}

?>
