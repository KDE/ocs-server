<?php

/*
 *   GFX 4
 * 
 *   support:	happy.snizzo@gmail.com
 *   website:	http://trt-gfx.googlecode.com
 *   credits:	Claudio Desideri
 *   
 *   This software is released under the MIT License.
 *   http://opensource.org/licenses/mit-license.php
 */ 

/*
 * This class represents a config file and can be used
 * to edit those files.
 * 
 * TODO: support also multivalue config files
 * 
 * example:
 * 
 * var1|data1
 * var2|data2
 * var3|data3
 * 
 */

class EConfigFile {
	
	private $name = false;
	private $header = '<?php die(); ?>';
	private $data = false;
	
	/** Set the config file based on local site configuration.
	 * 
	 * @param $name name of config file based on local file configuration without conf.php
	 * 
	 * @return EConfigFile object
	 */
	public function __construct($name=false){
		if($name!=false){
			$this->name = ELoader::$config_path.'/'.$name.'.conf.php';
			$this->set_abs_file($this->name);
		}
	}
	
	/**
	 * Used to set config absolute file path.
	 * Leave constructor empty and use this.
	 */
	public function set_abs_file($filename)
	{
		$this->name = $filename;
		if(!file_exists($this->name)){
			ELog::error('Config file not existent: '.$this->name);
		} else {
			$this->data = EConfig::parse_file($this->name);
		}
	}
	
	public function get_data()
	{
		return $this->data;
	}
	
	/*
	 * Gets a key value in config file if exists. Else return empty string.
	 */
	public function get($var){
		if(isset($this->data[$var])){
			return $this->data[$var];
		} else {
			return '';
		}
	}
	
	/*
	 * Modify or adds (if not present) a key/value pair on a config file
	 * Change the memory array, not the file on disk.
	 * In order to have it modified $this->save should be called.
	 */
	public function set($var, $value){
		$this->data[$var] = $value;
	}
	
	/**
	 * Save the representation in memory of the modified data
	 * in the actual config file.
	 */
	public function save()
	{
		$stream = fopen($this->name, 'w');
		fwrite($stream, $this->header."\n"); //writing protection header
		foreach($this->data as $key => $value){
			fwrite($stream, $key.'|'.$value."\n");
		}
		fclose($stream);
	}
	
	/*
	 * Change the memory array, not the file on disk.
	 */
	public function del($var){
		if(array_key_exists($var, $this->data)){
			//memory deletion
			unset($this->data[$var]);
			//rebuilding array indexes
			//$this->data = array_values($this->data);
		}
	}
}

?>
