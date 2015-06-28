<?php

/*
 *   GFX 4
 * 
 *   support:	happy.snizzo@gmail.com
 *   website:	http://www.gfx3.org
 *   credits:	Claudio Desideri
 *   
 *   This software is released under the MIT License.
 *   http://opensource.org/licenses/mit-license.php
 */
 
/*
 * This class aims to manage all the gfx config in general contained in gfx3/config.
 */

class EConfig{
	
	private static $safe_path;
	private static $config_path;
	public static $data;
	
	public static function load(){
		EConfig::$safe_path = getcwd();
		EConfig::load_all();
		//after loading all the confs restore previous working directory
		chdir(EConfig::$safe_path);
	}
	
	public static function print_debug_info(){
		var_dump(EConfig::$data);
	}
	
	/*
	 * Takes an array and returns the same array of strings without
	 * every string of php code.
	 */
	public static function erase_php_code($lines){
		$result = array();
		$inside = false;
		foreach($lines as $line){
			//if opening php string found, count as we're inside php code
			if(stristr($line, '<?php')){
				$inside = true;
			}
			//if this is not php code, copy line
			if(!$inside){
				$result[] = $line;
			}
			//if closing php string is found, we're not.
			if(stristr($line, '?>')){
				$inside = false;
			}
		}
		return $result;
	}
	
	/*
	 * returns a parsed file array mapped as
	 * $array[$key] = $value
	 */
	public static function parse_file($filename){
		//initializing empty array
		$result = array();
		
		//mapping file line per line
		//$cache = new ECacheFile($filename);
		//$file = $cache->get();
		//$file = explode("\n",$file);
		$file = file($filename);
		$file = EConfig::erase_php_code($file);
		foreach($file as $line){
			if(!empty($line)){
				$chunks = explode("|",$line);
				//gives correct key and correct value, erasing line break.
				//control if is set more than once
				if(isset($result[$chunks[0]])){
					//ELog::warning("<b>".$chunks[0]."</b> property is set more than once in ".$filename." config file!");
				} else
				//control if is set to empty value
				if(count($chunks)<2){
					ELog::warning("<b>".$chunks[0]."</b> property has empty value in ".$filename." config file!");
				} else
				//load 1:1 data and gives a simple value
				if(count($chunks)==2){
					$result[$chunks[0]] = rtrim($chunks[1], "\n");
				} else 
				//load 1:n data and gives an array
				if(count($chunks)>2){
					$data = array();
					for($i=1;$i<count($chunks);$i++){
						$data[] = rtrim($chunks[$i], "\n");
					}
					
					$result[$chunks[0]] = $data;
				} else {
					ELog::error("unknown error in EConfig loading");
				}
			}
		}
		return $result;
	}
	
	/*
	 * return an array with every config file merged
	 */
	public static function load_all(){
		EConfig::$data = array();
		//enters in conf directory
		chdir(ELoader::$config_path);
		//parse every single conf file and place it in an associative array
		foreach(glob("*") as $filename){
			$name = EFileSystem::get_file_name($filename);
			EConfig::$data[$name] = EConfig::parse_file($filename);
		}
	}
	
}

?>
