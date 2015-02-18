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
	private $login;
	private $password;
	private $uploaded_file = 0;
	private $postdata = 0;
	private $use_auth = false;
	private $last_raw_result = 0;
	
	public function __construct($srv="default"){
		if($srv=="default"){
			$this->set_target_server(EConfig::$data["ocs"]["host"]);
		} else {
			$this->set_target_server($srv);
		}
	}
	
	/**
	 * Check if string is json encoded.
	 */
	function is_json_encoded($string) {
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}
	
	/**
	 * Manually set target server for ocs client.
	 */
	public function set_target_server($srv){
		$this->target_server = rtrim($srv,"/")."/";
	}
	
	public function set_auth_info($login,$password){
		$this->login = $login;
		$this->password = $password;
		$this->use_auth = true;
	}
	
	public function generate_server(){
		if($this->use_auth){
			return "http://".$this->login.":".$this->password."@".$this->target_server;
		} else {
			return "http://".$this->target_server;
		}
	}
	
	/**
	 * Send a get request to the server.
	 */
	public function get($url){
		$server = $this->generate_server();
		$socket = new ENetworkSocket($server);
		$raw_data = $socket->get($url);
		//if result is in json format, parse json
		// else parse xml
		if($this->is_json_encoded($raw_data)){
			return json_decode($raw_data);
		} else {
			$data = EXmlParser::to_array($raw_data);
			return $data;
		}
	}
	
	public function set_upload_file($file){
		$this->uploaded_file = $file;
	}
	
	public function set_post_data($data){
		$this->postdata = $data;
	}
	
	public function get_last_raw_result(){
		if(!empty($this->last_raw_result)){
			return $this->last_raw_result;
		} else {
			ELog::warning("no request performed");
			return false;
		}
	}
	
	/**
	 * Send a post request to the server.
	 * $data should be provided as an associative array
	 * following this example:
	 * "name" => "value"
	 */
	public function post($url,$data=""){
		$server = $this->generate_server();
		$socket = new ENetworkSocket($server);
		
		//deprecated. use OCSClient->set_post_data($data) instead
		if(!empty($data)){ $socket->set_post_data($data); }
		//use this instead
		if($this->postdata){ $socket->set_post_data($this->postdata); }
		//eventual uploaded file
		if($this->uploaded_file){
			$socket->set_upload_file($this->uploaded_file);
		}
		
		$raw_data = $socket->post($url);
		//if result is in json format, parse json
		// else parse xml
		if($this->is_json_encoded($raw_data)){
			return json_decode($raw_data);
		} else {
			$this->last_raw_result = $raw_data;
			$data = EXmlParser::to_array($raw_data);
			return $data;
		}
	}
	
}

?>
