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
 * This module should provide a quite reasonable amount of security if used correctly.
 * It automatically parse incoming data, preventing from SQL injection etc...
 */

class EHeaderDataParser {
	
	private $gets;
	private $posts;
	private $quotes;
	
	private $main;
	
	/*
	 * Store all the keys  of gets and posts in arrays.
	 */
	public function __construct(){
		$this->main = EMain::getRef();
		$this->quotes = get_magic_quotes_gpc();
		$this->gets = $_GET;
		$this->posts = $_POST;
	}
	
	// Generates safe data for using in databases.
	public function safeAll(){
		if(!$this->quotes){
			foreach ($this->gets as $key => $value){
				$this->gets[$key] = $this->main->db->safe($value);
			}
			foreach ($this->posts as $key => $value){
				$this->posts[$key] = $this->main->db->safe($value);
			}
		}
	}
	
	public function exists_post($key){
		if(isset($this->posts[$key])){
			return true;
		} else {
			return false;
		}
	}
	
	public function exists_get($key){
		if(isset($this->gets[$key])){
			return true;
		} else {
			return false;
		}
	}
	
	public function out_get($key){
		if($this->quotes){
			return stripslashes($this->gets[$key]);
		} else {
			return $this->gets[$key];
		}
	}
	
	public function out_post($key){
		if($this->quotes){
			return stripslashes($this->posts[$key]);
		} else {
			return $this->posts[$key];
		}
	}
	
	// Use instead of accessing $_GET
	public function db_get($key){
		if($this->quotes){
			return $this->gets[$key];
		} else {
			return $this->main->db->safe($this->gets[$key]);
		}
	}
	
	
	// Use instead of accessing $_POST
	public function db_post($key){
		if($this->quotes){
			return $this->posts[$key];
		} else {
			return $this->main->db->safe($this->posts[$key]);
		}
	}
}
 
?>
