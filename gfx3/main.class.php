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

include_once("error.class.php");
include_once("structure.class.php");
include_once("cache.class.php");
include_once("data.class.php");
include_once("database.class.php");
include_once("filemod.class.php");
include_once("form.class.php");
include_once("slider.class.php");
include_once("user.class.php");
include_once("ocs_user.class.php");
include_once("utility.class.php");

/*
 * Main class used to handle all the other classes.
 * It automatically instantiates the needed objects
 * to run all the modes.
 */

class EMain {
	
	//time bench related var
	private $time_start;
	private $time_end;
	
	//singletons
	public $db;
	public $user;
	public $error;
	public $dbg;
	
	/*
	 * various settings to be chosen between database, user and template management.
	 * it always starts microtime to do some debug on performance
	 */
	public function __construct($mode="standard", $template="main") {
		//debug info only
		$this->time_start = microtime(true);
		
		//standard global objects
		$GLOBALS['emain'] = $this;
		$GLOBALS['edbg'] = $this->dbg = true;
		$GLOBALS['elog'] = $this->log = new EError(); //plain text, don't preserve as default
		$GLOBALS['edb'] = $this->db = new EDatabase(); //config in config.php
		$GLOBALS['euser'] = $this->user = new OCS_User(); //user compatible with the OCS protocol
		
	}
	
	/*
	 * show the actual time needed to generate the page and do some benchmark
	 */
	public function extime(){
		$time_end = microtime(true);
		$time = $time_end - $this->time_start;
		return $time;
	}
	
	/*
	 * ping function used to know if the object is really present in memory or not
	 */
	public function ping(){
		echo "DEBUG: EMain() is alive!";
		return true;
	}
	
	/*
	 * destructor
	 */
	public function __destruct(){
		// actually unsetting the objects will cause the page to be generated
		unset($this->structure);
		unset($this->user);
		unset($this->db);
	}
	
}

?>
