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
	
	private static $gets;
	private static $posts;
	private static $quotes;
	
	/*
	 * Store all the keys  of gets and posts in arrays.
	 */
	public static function load(){
		EHeaderDataParser::$quotes = get_magic_quotes_gpc();
		EHeaderDataParser::$gets = $_GET;
		EHeaderDataParser::$posts = $_POST;
	}
	
	// Generates safe data for using in databases.
	public static function safeAll(){
		if(!EHeaderDataParser::$quotes){
			foreach (EHeaderDataParser::$gets as $key => $value){
				EHeaderDataParser::$gets[$key] = EHeaderDataParser::$main->db->safe($value);
			}
			foreach ($this->posts as $key => $value){
				$this->posts[$key] = EDatabase::safe($value);
			}
		}
	}
	
	public static function exists_post($key){
		if(isset(EHeaderDataParser::$posts[$key])){
			return true;
		} else {
			return false;
		}
	}
	
	public static function exists_get($key){
		if(isset($this->gets[$key])){
			return true;
		} else {
			return false;
		}
	}
	
	public static function out_get($key){
		if($this->quotes){
			return stripslashes($this->gets[$key]);
		} else {
			return $this->gets[$key];
		}
	}
	
	public static function out_post($key){
		if($this->quotes){
			return stripslashes($this->posts[$key]);
		} else {
			return $this->posts[$key];
		}
	}
	
	// Use instead of accessing $_GET
	public static function db_get($key){
		if($this->quotes){
			return $this->gets[$key];
		} else {
			return EDatabase::safe($this->gets[$key]);
		}
	}
	
	
	// Use instead of accessing $_POST
	public static function db_post($key){
		if($this->quotes){
			return $this->posts[$key];
		} else {
			return EDatabase::safe($this->posts[$key]);
		}
	}
}
 
?>
