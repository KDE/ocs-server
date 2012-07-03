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
	private $debug = false;
	
	public function __construct($file=false){
		global $main;
		$this->main = $main;
		
		$this->file = "gfx3/cache/$file.chv";
		
		if(!file_exists($this->file)){
			$stream = fopen($this->file,'a+');
			fclose($stream);
		}
	}
	/* TODO: inspect.
	public function exists($file){
		if(file_exists($this->file)){
			return true;
		} else {
			return false;
		}
	}
	*/
	public function get($var){
		if($this->file){
			$content = file($this->file);
			foreach($content as $line){
				$line = explode("=", $line);
				if($line[0]==$var){
					return rtrim($line[1], "\n");
				}
			}
		} elseif($this->debug){
			$this->main->log->warning("trying to get cache data from non existent cache var file!");
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
		} elseif($this->debug){
			$this->main->log->warning("trying to set cache data from non existent cache var file!");
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
		} elseif($this->debug){
			$this->main->log->warning("trying to del cache data from non existent cache var file!");
		}
	}
	
}

?>
