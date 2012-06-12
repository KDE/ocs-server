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

?>
