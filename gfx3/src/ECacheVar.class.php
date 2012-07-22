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
	
	private $main;
	private $debug = true;
	
	public function __construct($name=false){
		$this->main = EMain::getRef();
		
		$this->name = "gfx3varcache_$name";
	}
	
	public static function exists($name){
		return apc_exists($name);
	}
	
	//returns the value associate to $var
	public function get($var){
		$content = apc_fetch($this->name);
		$content = explode("\n", $content);
		foreach($content as $line){
			$line = explode("|", $line);
			if($line[0]==$var){
				return rtrim($line[1], "\n");
			}
		}
	}
	
	public function set($var, $value){
		$content = apc_fetch($this->name);
		$content = explode("\n", $content);
		
		$has_been_inserted = false;
		$l = count($content);
		for($i=0;$i<$l;$i++){
			$line = explode("|", $content[$i]);
			if($line[0]==$var){
				$content[$i] = $var."|".$value;
				$has_been_inserted = true;
			}
		}
		
		if(!$has_been_inserted){
			$content[] = $var."|".$value;
		}
		
		$str = implode("\n", $content);
		apc_store($this->name, $str);
	}
	
	public function get_array_assoc(){
		$content = apc_fetch($this->name);
		$content = explode("\n", $content);
		
		$assoc = array();
		$l = count($content);
		for($i=0;$i<$l;$i++){
			if(!empty($content[$i])){
				$line = explode("|", $content[$i]);
				$assoc[$line[0]] = rtrim($line[1], "\n");
			}
		}
		return $assoc;
	}
	
	public function del($var){
		$content = apc_fetch($this->name);
		$content = explode("\n", $content);
		
		$l = count($content);
		for($i=0;$i<$l;$i++){
			$line = explode("|", $content[$i]);
			if($line[0]==$var){
				unset($content[$i]);
			}
		}
		
		$str = implode("\n", $content);
		apc_store($this->name, $str);
	}
	
	public function print_raw_cache(){
		echo apc_fetch($this->name);
	}
	
}

?>
