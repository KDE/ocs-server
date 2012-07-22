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

class ENetworkSocket{
	
	private $target_server;
	private $postdata;
	
	public function __construct($srv){
		$this->set_target_server($srv);
	}
	
	public function set_target_server($srv){
		$this->target_server = rtrim($srv,"/")."/";
	}
	
	/* Example post data:
	 * $postdata = http_build_query(
			array(
				'var1' => 'some content',
				'var2' => 'doh'
			)
		);
	 *
	 */
	public function set_post_data($data){
		$this->postdata = $data;
	}
	/*
	 * performs a get request and return raw xml
	 */
	public function get ($string){
		$url = $this->target_server.$string;
		$result = file_get_contents($url);
		return $result;
	}
	
	/*
	 * performs a post request and return raw xml
	 */
	public function post ($string){
		$opts = array('http' =>
			array(
				'method'  => 'POST',
				'header'  => 'Content-type: application/x-www-form-urlencoded',
				'content' => $this->postdata
			)
		);

		$context = stream_context_create($opts);
		$url = $this->target_server.$string;
		$result = file_get_contents($url, false, $context);
		return $result;
	}
}

?>
