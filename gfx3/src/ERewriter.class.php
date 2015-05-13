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
		//if web page is set to be not rewritable, simply return
		
		if(isset(EConfig::$data['rewrite'][$_SERVER['REQUEST_URI']])){
			$_SERVER['REQUEST_URI'] = EConfig::$data['rewrite'][$_SERVER['REQUEST_URI']];
		} else {
			return;
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
