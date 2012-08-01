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

class ELoader{
	
	public static $prev_path;
	public static $abs_path;
	public static $source_path = "gfx3/src";
	public static $cache_path = "gfx3/cache";
	public static $config_path = "gfx3/config";
	
	//look for gfx3 installation path
	public static function getLibInstallPath(){
		if(is_dir("gfx3")){
			ELoader::$cache_path = getcwd()."/".ELoader::$cache_path;
			ELoader::$config_path = getcwd()."/".ELoader::$config_path;
			return getcwd()."/".ELoader::$source_path;
		} else {
			chdir("..");
			return ELoader::getLibInstallPath();
		}
	}
	
	//load all modules dynamically
	public static function loadAllModules(){
		
		ELoader::$prev_path = getcwd();
		ELoader::$abs_path = ELoader::getLibInstallPath();
		chdir(ELoader::$abs_path);
		foreach(glob("*.class.php") as $filename){
			include_once($filename);
		}
		chdir(ELoader::$prev_path);
	}
}

class EUnloader{
	public function __destruct(){
		EDatabase::unload();
	}
}

$unloader = new EUnloader();

//including all modules
ELoader::loadAllModules();

//loading current website configuration
EConfig::load();

//rewrite url if needed
ERewriter::load();

//loading get/post
EHeaderDataParser::load();

//loading database
EDatabase::load();
?>
