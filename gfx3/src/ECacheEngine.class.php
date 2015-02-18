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

class ECacheEngine {
	
	private $main;
	
	private $cached;
	private $requested_page;
	
	public function __construct(){
		global $main;
		$this->main = $main;
		
		$this->cached = new EData("cached",$this->main);
		$this->requested_page = get_script_name();
	}
	
	public function exists_cache(){
		if($this->cached->count("script","script='".$this->requestedpage."'")){
			return true;
		} else {
			return false;
		}
	}
	/*
	 * update cache using sha1_file
	 */
	public function update_cache(){
		//
	}
	
	public function generate_cache(){
		//
	}
	
	public function get_cache(){
		if($this->exists_cache()){
			$this->cached->row("content","script='".$this->requested_page."'");
		} else {
			$this->main->log->warning("requested a cache for a non-cached page in ".$this->requested_page);
		}
	}
	
	public function get_script_name(){
		$script = $_SERVER['SCRIPT_NAME'];
		return $script;
	}
}

?>
