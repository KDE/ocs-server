<?php

/*
 *   GFX 4.0
 * 
 *   support:	happy.snizzo@gmail.com
 *   website:	http://trt-gfx.googlecode.com
 *   credits:	Claudio Desideri
 *   
 *   This software is released under the MIT License.
 *   http://opensource.org/licenses/mit-license.php
 */ 


//starting session with cookies
session_start();

/*
 * Small module includer. Used to include php modules automatically.
 */

class ELoader{
	
	public static $prev_path; // /var/www/html
	public static $abs_path; // /var/www/html/gfx/src
	public static $root_path; // localhost
	public static $source_path = "gfx3/src";
	public static $cache_path = "gfx3/cache";
	public static $libs_path = "gfx3/libs";
	public static $config_path = "config";
	public static $controllers_path = "controllers";
	public static $models_path = "models";
	public static $views_path = "views";
	public static $locallibs_path = "libs";
	
	//actual website path, containg config, models, views and controllers
	public static $site_path = "";
	public static $setted_config = false;
	
	//look for gfx3 installation path
	public static function getLibInstallPath(){
		if(is_dir(ELoader::$config_path) and !ELoader::$setted_config){
			ELoader::$config_path = getcwd()."/".ELoader::$config_path;
			ELoader::$controllers_path = getcwd()."/".ELoader::$controllers_path;
			ELoader::$models_path = getcwd()."/".ELoader::$models_path;
			ELoader::$views_path = getcwd()."/".ELoader::$views_path;
			ELoader::$locallibs_path = getcwd()."/".ELoader::$locallibs_path;
			ELoader::$setted_config = true;
		}
		if(is_dir("gfx3")){
			ELoader::$cache_path = getcwd()."/".ELoader::$cache_path;
			ELoader::$libs_path = getcwd()."/".ELoader::$libs_path;
			return getcwd()."/".ELoader::$source_path;
		} else {
			chdir("..");
			return ELoader::getLibInstallPath();
		}
	}
	
	/*
	 * This method gives precedence to sub-websites
	 * located in subfolders when the url is called
	 * and set the correct path for loading:
	 * config, controllers, models and views
	 * in the subdirectory.
	 */
	public static function checkForSubsites(){
		$current_uri = $_SERVER['REQUEST_URI'];
		$dirs = explode("/", $current_uri);
		
		foreach($dirs as $key => $dir){
			if(is_dir($dir)){
				chdir($dir);
				if(is_dir("config")){
					//url from gfxroot to config, controllers etc...
					$base_url = implode("/", array_slice($dirs, 0, $key+1));
					
					$pos = strpos($_SERVER['REQUEST_URI'],$base_url);
					if ($pos !== false) {
						if($pos==0){
							$rewritten = str_replace($base_url, "", $_SERVER['REQUEST_URI']);
							$_SERVER['REQUEST_URI'] = $rewritten;
						}
					}
					
					ELoader::$site_path = getcwd();
					break;
				}
			}
		}
	}
	
	//load all modules dynamically
	public static function loadAllModules(){
		
		ELoader::$root_path = $_SERVER["HTTP_HOST"];
		ELoader::$prev_path = getcwd();
		
		//handle eventual subsites, changes directory and leaves unchanged
		//the engine will later check for gfx presence in an upper folder
		ELoader::checkForSubsites();
		
		ELoader::$abs_path = ELoader::getLibInstallPath();
		
		//include source
		if(chdir(ELoader::$abs_path)){
			foreach(glob("*.class.php") as $filename){
				include_once($filename);
			}
		} else {
			ELog::error("critical error including gfx source. Path: ".ELoader::$abs_path);
		}
		//include controllers
		if(chdir(ELoader::$controllers_path)){
			foreach(glob("*.controller.php") as $filename){
				include_once($filename);
			}
		} else {
			ELog::error("critical error including controllers. Path: ".ELoader::$controllers_path);
		}
		
		//include models
		if(chdir(ELoader::$models_path)){
			foreach(glob("*.model.php") as $filename){
				include_once($filename);
			}
		} else {
			ELog::error("critical error including models. Path: ".ELoader::$models_path);
		}
		
		//include local external libraries
		//this is optional, if not found nothing happens
		if(file_exists(ELoader::$locallibs_path)){
			if(chdir(ELoader::$locallibs_path)){
				foreach(glob("*.class.php") as $filename){
					include_once($filename);
				}
			} else {
				ELog::error("critical error including controllers. Path: ".ELoader::$locallibs_path);
			}
		}
		
		//include global external libraries
		if(chdir(ELoader::$libs_path)){
			foreach(glob("*.class.php") as $filename){
				include_once($filename);
			}
		} else {
			ELog::error("critical error including external libs. Path: ".ELoader::$libs_path);
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
if (EConfig::$data['generic']['rewrite'] == "yes"){
	ERewriter::enable();
	ERewriter::load();
} else {
	ERewriter::disable();
}

//loading get/post
if (EConfig::$data['generic']['protectheaders'] == "yes"){
	EHeaderDataParser::load();
}

//loading database
if (EConfig::$data['generic']['database'] == "yes"){
	EDatabase::load();
}

//loading user system
if (EConfig::$data['generic']['users'] == "yes"){
	OCSUser::client_login();
}

//rendering the page
if (EConfig::$data['generic']['mvc'] == "yes"){
	if (EConfig::$data['generic']['rewrite'] == "yes"){
		EStructure::render(); //rendering default page
	} else {
		ELog::error("You must activate 'rewrite' module under config/generic before using 'mvc'!");
		return;
	}
}

?>
