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
	
	/*
	 * Tries to render a method's controller
	 */
	public static function render($url="")
	{
		//setting default 
		if(empty($url)){
			$url = $_SERVER['REQUEST_URI'];
		}
		
		//getting correct input
		$uri = explode("?", $url); //ignoring all normal get part as for now
		$input = trim($uri[0], "/");
		$chunks = explode("/", $input);
		
		if(isset($chunks[0])){ $controller = $chunks[0];		unset($chunks[0]); }
		if(isset($chunks[1])){ $method = $chunks[1];			unset($chunks[1]); }
		
		//building controller name
		$controller = ucfirst($controller)."Controller";
		
		$args = $chunks;
		$string_args = array();
		
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
		
		/* TODO: reevaluate this
		//the final arrays cointains also the string version of the argument
		//eg. $args['save'] = 0;
		foreach($args as $arg){
			$string_args[$arg] = 1; //just some random data
		}
		*/
		
		//checking if controller is available
		if (class_exists($controller)){
			$current_controller = new $controller();
			$current_controller->set_args($args);
			
			if(empty($method)){
				if(method_exists($current_controller, 'index')){
					$current_controller->index($chunks);
				}
			} else {
				if($method[0]!='_'){ //makes methods with _ not callable
					if(method_exists($current_controller,$method)){
						$current_controller->$method($args);
					} else {
						ELog::warning($controller."->".$method."() is not defined. Please define it.");
					}
				}
			}
		} else {
			ELog::warning($controller." class is not defined. Please define it.");
		}
		
	}
	
	/*
	 * Used to load controllers inside other views.
	 * $path		contains relative url from views folder
	 * [$args]	contains data arguments that can be used
	 * 			in view with data[0], data[1] etc...
	 * 			inside view
	 * 
	 * return	void  
	 */
	public static function controller($path){
		echo "calling $path<br>";
	}
	
	/*
	 * Used to load views from a controller and inside 
	 * other views.
	 * $url		contains relative url from views folder
	 * [$args]	contains data arguments that can be used
	 * 			in view with data[0], data[1] etc...
	 * 			inside view
	 * 
	 * return	void  
	 */
	public static function view($url)
	{
		$numargs = func_num_args();
		$arg_list = func_get_args();
		$data = array();
		
		for ($i = 1; $i < $numargs; $i++) {
			$data[] = $arg_list[$i];
		}
		
		$filepath = ELoader::$views_path."/$url.views.php";
		
		if(file_exists($filepath)){
			include(ELoader::$views_path."/$url.views.php");
		} else {
			ELog::error("non-existent view included. Please define $filepath !");
		}
	}
	
}

?>
