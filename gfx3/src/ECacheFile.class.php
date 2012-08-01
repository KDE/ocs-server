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

class ECacheFile {
	
	private $var;
	private $prefix = "ecachefile_";
	
	public function __construct($file=false){
		//if cache doesn't exists, load cache from text file
		if(!$this->exists($this->prefix.$file)){
			$this->var = $file;
			if(file_exists($file)){
				$content = file_get_contents($file);
				$this->set($content);
			}
		}
	}
	
	public function exists($key){
		apc_exists($this->prefix.$key);
	}
	
	// get the value of a variable
	public function get(){
		return apc_fetch($this->prefix.$this->var);
	}
	
	//set the value of a variable
	public function set($value){
		apc_store($this->prefix.$this->var,$value);
	}
	
	public function del(){
		apc_delete($this->prefix.$this->var);
	}
}
