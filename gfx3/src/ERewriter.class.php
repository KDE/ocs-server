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
 * This class aims to do some quite simple URL Rewriting.
 */

class ERewriter{
	
	public static $rewritable = false;
	
	/*
	 * Rewrite url if needed.
	 */
	public static function load()
	{
		//treat url erasing extra parts
		$current_uri = $_SERVER['REQUEST_URI'];
		
		foreach(EConfig::$data['rewrite'] as $key => $value){
			if(!is_array($value)){
				ELog::warning("You need to specify a second value as a rewrite rule in rewrite.conf.php");
				return;
			}
			
			//handle with normal rewrite that
			//permits to have parameters
			if($value[1]=="normal"){			
				$pos = strpos($current_uri,$key);
				
				//if we found a rewrite rule that can be applied
				if ($pos !== false) {
					//rewrite only at the very start of the string
					if($pos==0){
						$rewritten = str_replace($key, $value[0], $current_uri); //TODO FIX HERE FIRST OCCURRENCE
						//$rewritten = preg_replace("$key", $value, $current_uri, 1);
						$_SERVER['REQUEST_URI'] = $rewritten;
					}
				}
			}
			//handle with exact match, that works only if the url is exactly that one
			else if($value[1]=="exact"){
				$current_uri_t = explode("?", $current_uri)[0];
				if($current_uri_t==$key){
					$rewritten = str_replace($key, $value[0], $current_uri); //TODO FIX HERE
					//$rewritten = preg_replace("$key", $value, $current_uri, 1);
					$_SERVER['REQUEST_URI'] = $rewritten;
				}
			}
			
		}
	}

	/**
	* Used to make url pretty in order to SEO
	*/
	public static function prettify($string){
		$string = preg_replace("/[^a-z_\-0-9]/i", "-", $string);
		$string = preg_replace("/[-]+/", "-", $string);
		$string = trim($string, "-");
		return $string;
	}
}

?>
