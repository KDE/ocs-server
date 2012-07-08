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

class OCSFan {
	
	private $main;
	
	public function __construct(){
		global $main;
		$this->main = $main;
	}
	
	public function add($content){
		$person = $this->main->user->id();
		$this->main->db->q("INSERT INTO ocs_fan (person,content) VALUES ($person,$content)");
	}
	
	public function remove($content){
		//
	}
	
	public function isfan($content){
		//
	}
	
	public function get($content){
		//
	}
	
}

?>
