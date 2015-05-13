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
	public static $controllers_path = "controllers";
	public static $models_path = "models";
	public static $views_path = "views";
	
	//look for gfx3 installation path
	public static function getLibInstallPath(){
		if(is_dir("gfx3")){
			ELoader::$cache_path = getcwd()."/".ELoader::$cache_path;
			ELoader::$config_path = getcwd()."/".ELoader::$config_path;
			ELoader::$controllers_path = getcwd()."/".ELoader::$controllers_path;
			ELoader::$models_path = getcwd()."/".ELoader::$models_path;
			ELoader::$views_path = getcwd()."/".ELoader::$views_path;
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
		
		//include source
		chdir(ELoader::$abs_path);
		foreach(glob("*.class.php") as $filename){
			include_once($filename);
		}
		//include controllers
		chdir(ELoader::$controllers_path);
		foreach(glob("*.controller.php") as $filename){
			include_once($filename);
		}
		//include models
		chdir(ELoader::$models_path);
		foreach(glob("*.model.php") as $filename){
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
if (EConfig::$data['generic']['rewrite'] == "yes"){
	ERewriter::load();
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
	EUser::load();
}

//rendering the page
if (EConfig::$data['generic']['mvc'] == "yes"){
	if (EConfig::$data['generic']['rewrite'] == "yes"){
		EStructure::render();
	} else {
		ELog::error("You must activate 'rewrite' module under config/generic before using 'mvc'!");
		return;
	}
}

?>
