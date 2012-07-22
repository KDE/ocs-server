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


/*
 * Small module includer. Used to include php modules automatically.
 */

class EIncluder{
	
	public static $prev_path;
	public static $abs_path;
	public static $source_path = "gfx3/src";
	public static $cache_path = "gfx3/cache";
	
	//look for gfx3 installation path
	public static function getLibInstallPath(){
		if(is_dir("gfx3")){
			EIncluder::$cache_path = getcwd()."/".EIncluder::$cache_path;
			return getcwd()."/".EIncluder::$source_path;
		} else {
			chdir("..");
			return EIncluder::getLibInstallPath();
		}
	}
	
	//load all modules dynamically
	public static function loadAllModules(){
		
		EIncluder::$prev_path = getcwd();
		EIncluder::$abs_path = EIncluder::getLibInstallPath();
		chdir(EIncluder::$abs_path);
		foreach(glob("*.class.php") as $filename){
			include_once($filename);
		}
		chdir(EIncluder::$prev_path);
	}
	
}

//including all modules
EIncluder::loadAllModules();

/*
 * Main class used to handle all the other classes.
 * It automatically instantiates the needed objects
 * to run all the modes.
 */

class EMain {
	
	//time bench related var
	private $time_start;
	private $time_end;
	public static $ref;
	
	//singletons
	
	/*
	 * various settings to be chosen between database, user and template management.
	 * it always starts microtime to do some debug on performance
	 */
	public function __construct($mode="standard", $template="main") {
		//debug info only
		$this->time_start = microtime(true);
		
		//standard global objects
		EMain::$ref = $this;
		
		$this->dbg = true;
		$this->db = new EDatabase(); //config in config.php TODO:fix
		$this->data = new EHeaderDataParser();
		
	}
	
	public static function getRef(){
		return EMain::$ref;
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
		//
		// nothing to be done here yet...
	}
	
}

?>
