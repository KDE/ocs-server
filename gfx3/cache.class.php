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
class ECacheList {
	
	private $main;
	private $file = false;
	
	public function __construct($file=false){
		global $main;
		$this->main = $main;
		
		$this->file = "gfx3/cache/$file.chl";
		
		if(!file_exists($this->file)){
			$stream = fopen($this->file,'a+');
			fclose($stream);
		}
	}
	
	public function exists($file){
		if(file_exists("gfx3/cache/$file.chl")){
			return true;
		} else {
			return false;
		}
	}
	
	public function get(){
		if($this->file){
			$content = file($this->file);
			$return = array();
			foreach($content as $line){
					$return[] = rtrim($line, "\n");
			}
			return $return;
		} elseif($this->main->dbg) {
			$this->main->log->warning("trying to get cache data from non existent cache list!");
		}
	}
	
	public function add($value){
		if($this->file){
			$content = file($this->file);
			$stream = fopen($this->file,'a+');
			fwrite($stream,$value."\n");
			fclose($stream);
		} elseif($this->main->dbg) {
			$this->main->log->warning("trying to add cache data from non existent cache list!");
		}
	}
	
	public function clear(){
		if($this->file){
			unlink($this->file);
		} elseif($edbg->main->dgb) {
			$elog->warning("trying to clear cache data from non existent cache list!");
		}
	}
	
}

/*
 * This class stores elements of a list in a text file for caching.
 * 
 * example:
 * 
 * var1=data1
 * var2=data2
 * var3=data3
 * 
 */

class ECacheVar {
	
	private $main;
	private $file = false;
	
	public function __construct($file=false){
		global $main;
		$this->main = $main;
		
		$this->file = "gfx3/cache/$file.chv";
		
		if(!file_exists($this->file)){
			$stream = fopen($this->file,'a+');
			fclose($stream);
		}
	}
	
	public function exists($file){
		if(file_exists($this->file)){
			return true;
		} else {
			return false;
		}
	}
	
	public function get($var){
		if($this->file){
			$content = file($this->file);
			foreach($content as $line){
				$line = explode("=", $line);
				if($line[0]==$var){
					return rtrim($line[1], "\n");
				}
			}
		} elseif($this->main->edbg){
			$elog->warning("trying to get cache data from non existent cache var file!");
		}
	}
	
	public function set($var, $value){
		if($this->file){
			$content = file($this->file);
			
			foreach($content as $pos => $line){
				$line = explode("=", $line);
				if($line[0]==$var){
					$at = $pos;
				}
			}
			
			if(!isset($at)){
				$stream = fopen($this->file, 'a+');
				fwrite($stream, $var."=".$value."\n");
				fclose($stream);
			} else {
				$lenght = count($content);
				unlink($this->file);
				$stream = fopen($this->file, 'a+');
				for($i=0; $i<$lenght;$i++){
					if($i==$at){
						fwrite($stream, $var."=".$value."\n");
					} else {
						fwrite($stream, $content[$i]);
					}
				}
				fclose($stream);
			}
		} elseif($this->main->edbg){
			$elog->warning("trying to set cache data from non existent cache var file!");
		}
	}
	
	public function del($var){
		if($this->file){
			$content = file($this->file);
			
			foreach($content as $pos => $line){
				$line = explode("=", $line);
				if($line[0]==$var){
					$at = $pos;
				}
			}
			
			if(isset($at)){
				$lenght = count($content);
				unlink($this->file);
				$stream = fopen($this->file, 'a+');
				for($i=0; $i<$lenght;$i++){
					if($i!=$at){
						fwrite($stream, $content[$i]);
					}
				}
				fclose($stream);
			}
		} elseif($this->main->edbg){
			$elog->warning("trying to del cache data from non existent cache var file!");
		}
	}
	
}
