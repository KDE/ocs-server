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
		$uri = explode("?", $_SERVER['REQUEST_URI']); //ignoring all normal get part as for now
		$input = trim($uri[0], "/");
		$chunks = explode("/", $input);
		
		if(isset($chunks[0])){ $controller = $chunks[0];		unset($chunks[0]); }
		if(isset($chunks[1])){ $method = $chunks[1];			unset($chunks[1]); }
		
		//building controller name
		$controller = ucfirst($controller)."Controller";
		
		$args = $chunks;
		
		//resetting array index and getting last element
		$args = array_values($args);
		$last = end($args);
		$extraargs = explode("?", $last);
		if(count($extraargs)>=2){
			unset($args[count($args)-1]); //erase last raw unparsed element
			
			//add last element
			if(!empty($extraargs[1])){
				$args[] = $extraargs[0];
			}
			
			//manually injecting get data
			EHeaderDataParser::add_from_string($extraargs[1]);
		}
		
		//checking if controller is available
		if (class_exists($controller)){
			$current_controller = new $controller();
			
			if(empty($method)){
				$current_controller->index($chunks);
			} else {
				if(method_exists($current_controller,$method)){
					$current_controller->$method($args);
				} else {
					ELog::warning($controller."->".$method."() is not defined. Please define it.");
				}
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
