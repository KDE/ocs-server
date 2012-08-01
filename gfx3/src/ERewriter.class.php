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
 * This class aims to do some quite simple URL Rewriting.
 */

class ERewriter{
	
	public static $rewritable = false;
	
	/*
	 * Rewrite url if needed.
	 */
	public static function load(){
		//if web page is set to be not rewritable, simply return
		if(!ERewriter::is_rewritable()){
			//ELog::warning("page is set to be <b>not</b> rewritable");
			return;
		}
		
		$get_request = rtrim($_SERVER["REQUEST_URI"],"/");
		
		$chunks = explode("/", $get_request);
		
		foreach($chunks as $key => $chunk){
			if(substr($chunk, -4) != ".php"){
				unset($chunks[$key]);
			} else {
				unset($chunks[$key]);
				break;
			}
		}
		
		$chunks = ERewriter::rebuild_keys($chunks);
		
		//manually assigning values to gets
		for($i=0;$i<count($chunks);$i+=2){
			$_GET[$chunks[$i]] = $chunks[$i+1];
		}
		
	}
	
	/*
	 * rebuild array keys
	 */
	 public static function rebuild_keys($array){
		 $i = 0;
		 foreach($array as $key => $value){
			 unset($array[$key]);
			 $array[$i] = $value;
			 $i++;
		 }
		 return $array;
	 }
	 
	 public static function is_rewritable(){
		 $page_name = EPageProperties::get_page_name();
		 if(isset(EConfig::$data['rewrite'][$page_name])){
			 if(EConfig::$data['rewrite'][$page_name]=="true"){
				 ERewriter::$rewritable = true;
				 return true;
			 } else {
				 ERewriter::$rewritable = false;
				 return false;
			 }
		 } else {
			 //assume by default that webpages are not rewritable
			 return false;
		 }
	 }
}

?>
