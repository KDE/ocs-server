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
 * This class stores elements of a list in a text file for caching.
 * 
 * example:
 * 
 * data1
 * data2
 * data3
 * 
 */
/*
 * Just see if this can go to ECacheVar
 */
class ECacheList {
	
	private $main;
	private $prefix = "ecachelist_";
	
	public function __construct($file=false){
		$this->main = EMain::getRef();
	}
	
	public function exists($key){
		apc_exists($this->prefix.$key);
	}
	
	// get the value of a variable
	public function get($var){
		apc_fetch($foo);
	}
	
	//set the value of a variable
	public function set($var, $value){
		apc_store($this->prefix.$var,$value);
	}
	
	public function del($var){
		apc_delete($this->prefix.$var);
	}
}

?>
