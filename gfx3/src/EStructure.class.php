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
 *
 */

class EStructure {
	
	private static $debug = false;
	
	public static function render()
	{
		//getting correct input
		$input = trim($_SERVER['REQUEST_URI'], "/");
		$chunks = explode("/", $input);
		
		if(isset($chunks[0])){ $controller = $chunks[0];		unset($chunks[0]); }
		if(isset($chunks[1])){ $method = $chunks[1];			unset($chunks[1]); }
		
		//building controller name
		$controller = ucfirst($controller)."Controller";
		
		$args = $chunks;
		
		//checking if controller is available
		if (class_exists($controller)){
			$current_controller = new $controller();
			
			if(empty($method)){
				$current_controller->index($chunks);
			} else {
				$current_controller->$method($chunks);
			}
		}
		
	}
	
	public static function view($url, $data="")
	{
		$filepath = ELoader::$views_path."/$url.views.php";
		
		if(file_exists($filepath)){
			include(ELoader::$views_path."/$url.views.php");
		} else {
			ELog::error("non-existent view included. Please define $filepath !");
		}
	}
	
}

?>
