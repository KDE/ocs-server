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
	private $numpost = 0;
	private $uploaded_file = 0;
	
	public function __construct($srv){
		$this->set_target_server($srv);
	}
	
	public function set_target_server($srv){
		$this->target_server = rtrim($srv,"/")."/";
	}
	
	/* Example post data:
	 * $postdata = (
			array(
				'var1' => 'some content',
				'var2' => 'doh'
			)
		);
	 *
	 */
	public function set_post_data($data){
		$this->postdata = http_build_query($data);
		$this->numpost = count($data);
	}
	
	public function set_upload_file($file){
		$this->uploaded_file = $file;
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
		if($this->uploaded_file){
			$this->postdata['localfile'] = "@".$this->uploaded_file;

			$ch = curl_init();
			
			curl_setopt($ch, CURLOPT_URL, $this->target_server.$string ); // imposto l'URL dello script destinatario
			curl_setopt($ch, CURLOPT_POST, true ); // indico il tipo di comunicazione da effettuare (POST)
			curl_setopt($ch, CURLOPT_POSTFIELDS, $this->postdata); // indico i dati da inviare attraverso POST
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);
			
			if (curl_errno($ch)) { print curl_error($ch); }
			curl_close($ch); // close CURL session

			return $result;
			
		} else {
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
}

?>
