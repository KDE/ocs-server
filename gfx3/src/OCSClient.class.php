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
 * This class is used to serve as an OCS compatible client.
 * Takes $url and $data (as for post data) and returns an associative array
 * with data returned mapped in it.
 */
class OCSClient{
	
	private $target_server;
	
	function is_json_encoded($string) {
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}
	
	public function __construct($srv){
		$this->set_target_server($srv);
	}
	
	public function set_target_server($srv){
		$this->target_server = rtrim($srv,"/")."/";
	}
	
	public function get($url){
		$s = new ENetworkSocket($this->target_server);
		$raw_data = $s->get($url);
		//if result is in json format, parse json
		// else parse xml
		if($this->is_json_encoded($raw_data)){
			return json_decode($raw_data);
		} else {
			return EXmlParser::to_array($raw_data);
		}
	}
	
	public function post($url,$data){
		$s = new ENetworkSocket($this->target_server);
		$s->set_post_data($data);
		$raw_data = $s->post($url);
		//if result is in json format, parse json
		// else parse xml
		if($this->is_json_encoded($raw_data)){
			return json_decode($raw_data);
		} else {
			return EXmlParser::to_array($raw_data);
		}
	}
	
}

?>
