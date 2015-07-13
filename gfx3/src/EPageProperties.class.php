<?php

/*
 *   TRT GFX 3.0.1 (beta build) BackToSlash
 * 
 *   support:	happy.snizzo@gmail.com
 *   website:	http://www.gfx3.org
 *   credits:	Claudio Desideri
 *   
 *   This software is released under the MIT License.
 *   http://opensource.org/licenses/mit-license.php
 */ 


/*
 * Static class used as a helper for managing various web page properties.
 */
class EPageProperties {
	
	public static $request_uri = '';
	
	public static function get_page_name(){
		
		$name = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
		$name = substr($name,strrpos($name,"?"));
		$name = substr($name,strrpos($name,"/"));
		
		return $name;
	}
	
	/*
	 * Returns the exact website in which is running this system.
	 * For example: http://www.gfx3.org
	 */
	public static function get_current_website_url(){
		$pageURL = 'http';
		//TODO: implements https?
		$pageURL .= "://";
		$pageStripped = explode("/", $_SERVER["REQUEST_URI"]);
		$pageStripped = $pageStripped[0];
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"].$pageStripped;
		}
		
		return $pageURL;
	}
	
	public static function get_previous_page(){
		$prevpage = $_SERVER['HTTP_REFERER'];
		$prevpage = EHeaderDataParser::erase_get_data($prevpage);
		return $prevpage;
	}
	
}

?>
