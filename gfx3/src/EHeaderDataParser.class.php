<?php

/*
 *   TRT GFX 4.0
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
				EHeaderDataParser::$gets[$key] = EDatabase::safe($value);
			}
			foreach (EHeaderDataParser::$posts as $key => $value){
				EHeaderDataParser::$posts[$key] = EDatabase::safe($value);
			}
		}
	}
	
	/*
	 * Access untouched data
	 */
	public static function post($key){
		if(isset(EHeaderDataParser::$posts[$key])){
			return EHeaderDataParser::$posts[$key];
		} else {
			return false;
		}
	}
	
	public static function get($key){
		if(isset(EHeaderDataParser::$gets[$key])){
			return EHeaderDataParser::$gets[$key];
		} else {
			return false;
		}
	}
	
	public static function get_cookie($key){
		if(isset($_COOKIE[$key])){
			return $_COOKIE[$key];
		} else {
			return false;
		}
	}
	
	public static function set_cookie($key, $value, $time=86400){
		setcookie($key,$value, time()+($time * 30), "/"); //TODO: seriously inspect on time being multiplied for 30
	}

	public static function del_cookie($key){
		if(isset($_COOKIE[$key])){
			setcookie($key, "", -1, "/");
		}
	}
	
	/*
	 * Used to check if get/post has been set
	 */
	public static function exists_post($key){
		if(isset(EHeaderDataParser::$posts[$key])){
			return true;
		} else {
			return false;
		}
	}
	
	public static function exists_get($key){
		if(isset(EHeaderDataParser::$gets[$key])){
			return true;
		} else {
			return false;
		}
	}
	
	/*
	 * Useful if get or post need to be printed in html pages
	 */
	public static function out_get($key){
		if(isset(EHeaderDataParser::$gets[$key])){		
			if(EHeaderDataParser::$quotes){
				return stripslashes(EHeaderDataParser::$gets[$key]);
			} else {
				return EHeaderDataParser::$gets[$key];
			}
		} else {
			return false;
		}
	}
	
	public static function out_post($key){
		if(isset(EHeaderDataParser::$gets[$key])){		
			if(EHeaderDataParser::$quotes){
				return stripslashes(EHeaderDataParser::$posts[$key]);
			} else {
				return EHeaderDataParser::$posts[$key];
			}
		} else {
			return false;
		}
	}
	
	/*
	 * Safe parsed data to be used with databases 
	 */
	// Use instead of accessing $_GET
	//obsolete? port to secure_get
	public static function db_get($key){
		if(isset(EHeaderDataParser::$gets[$key])){
			if(EHeaderDataParser::$quotes){
				return EHeaderDataParser::$gets[$key];
			} else {
				return EDatabase::safe(EHeaderDataParser::$gets[$key]);
			}
		} else {
			return false;
		}
	}
	//usability rename
	public static function secure_get($key){
		return EHeaderDataParser::db_get($key);
	}
	
	
	// Use instead of accessing $_POST
	//obsolete? port to secure_post
	public static function db_post($key){
		if(isset(EHeaderDataParser::$posts[$key])){
			if(EHeaderDataParser::$quotes){
				return EHeaderDataParser::$posts[$key];
			} else {
				return EDatabase::safe(EHeaderDataParser::$posts[$key]);
			}
		} else {
			return false;
		}
	}
	//usability rename
	public static function secure_post($key){
		return EHeaderDataParser::db_post($key);
	}
	
	/*
	 * Manually adding values to module
	 * 
	 * Can be useful when using EModel automatic database management
	 */
	public static function add_post($key,$value){
		if(!isset(EHeaderDataParser::$posts[$key])){
			EHeaderDataParser::$posts[$key] = $value;
		} //else ignored
	}
	
	public static function add_get($key,$value){
		if(!isset(EHeaderDataParser::$gets[$key])){
			EHeaderDataParser::$gets[$key] = $value;
		} //else ignored
	}
	
	/*
	 * Loads GET/POST data from parsing an html string
	 */
	public static function add_from_string($str){
		$chunks = explode("&", $str);
		
		foreach($chunks as $chunk){
			$data = explode("=", $chunk);
			EHeaderDataParser::add_get($data[0],$data[1]);
		}
	}
	
	/*
	 * Simply returns page without additional data.
	 * Maybe to be moved to EPageProperties?
	 */
	public static function erase_get_data($url){
		$url = explode("?", $url);
		return $url[0];
	}
}
 
?>
