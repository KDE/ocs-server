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
 * This class stores elements of a list in an APC cache.
 * 
 * example:
 * 
 * var1|data1
 * var2|data2
 * var3|data3
 * 
 */

class ECacheVar {
	
	private $name = false;
	
	private $debug = true;
	
	public function __construct($name=false){
		
	}
	
	public static function exists($name){
		return false;
	}
	
	//returns the value associate to $var
	public function get($var){
		return "Cache fallback is failing!";
	}
	
	public function set($var, $value){
	}
	
	public function get_array_assoc(){
		return array("null"=>"cache fallback is failing!");
	}
	
	public function del($var){
	}
	
	public function print_raw_cache(){
		echo "WARNING: cache fallback is failing!";
	}
	
}

?>
