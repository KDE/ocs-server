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
				EHeaderDataParser::$gets[$key] = EDatabase::safe($value);
			}
			foreach (EHeaderDataParser::$posts as $key => $value){
				EHeaderDataParser::$posts[$key] = EDatabase::safe($value);
			}
		}
	}
	
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
	
	// Use instead of accessing $_GET
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
	
	
	// Use instead of accessing $_POST
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
	
	public static function erase_get_data($url){
		$url = explode("?", $url);
		return $url[0];
	}
}
 
?>
