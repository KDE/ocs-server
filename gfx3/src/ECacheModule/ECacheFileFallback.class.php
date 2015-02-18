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
 * The fallback module is provided just for compatibility.
 * 
 * In any way this is considered to be a cache system. It will simply force
 * the engine to ask for data every time.
 */

class ECacheFile {
	
	private $var = "";
	private $prefix = "ecachefile_";
	private $content = "";
	
	public function __construct($file=false){
		$this->var = $file;
		
		if(file_exists($this->var)){
			$this->content = file_get_contents($this->var);
		}
	}
	
	// get the value of a variable
	public function get(){
		return $this->content;
	}
	
	//set the value of a variable
	public function set($value){
		//does this really do something? :S
	}
	
	public function del(){
		//also, does this really do something? :S
	}
}
