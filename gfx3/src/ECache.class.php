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

define('APC_EXTENSION_LOADED', extension_loaded('apc') && ini_get('apc.enabled'));

if(APC_EXTENSION_LOADED==true){
	//load APC based cache sytem
	include_once('ECacheModule/ECacheFile.class.php');
	include_once('ECacheModule/ECacheList.class.php');
	include_once('ECacheModule/ECacheVar.class.php');
} else {
	//load fallback system, provided just for compatibility
	include_once('ECacheModule/ECacheFileFallback.class.php');
	include_once('ECacheModule/ECacheListFallback.class.php');
	include_once('ECacheModule/ECacheVarFallback.class.php');
}
	

?>
