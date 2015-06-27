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

class EUtility {
	
	//actually this function is needed by some files, but I don't actually know why.
	//and what this function did. Put this for now, but can generate errors.
	public static function stripslashes($string) {
		return str_replace('\\','',$string);
	}
	
	public static function br2nl($string) { 
		return str_replace('<br>', '\r\n', $string);
	}
	
	public static function nl2br($string) {
		$string = str_replace('\r\n', '<br>', $string);
		$string = str_replace('\r', '<br>', $string);
		$string = str_replace('\n', '<br>', $string);
		return $string;
	}
	
	/*Use this function to protect your webpage.
	* this works by adding those properties to local generic.conf.php:
	* 
	* enabled|yes
	* enabled|no
	* enabled|protected
	* 
	* which can be 'yes' or 'no'. If nonsense is written, gfx will keep no
	* as default.
	* 
	* password|yourpassword
	* 
	* which will be your password that you have to pass with ?password=yourpassword
	* in your get requests.
	* 
	*/
	public static function protect()
	{
		//case in which it is 'no' or anything different from 'yes' or 'protected'
		if(EConfig::$data['generic']['enabled']!='yes' and EConfig::$data['generic']['enabled']!='protected'){
			die('Access denied.');
		}
		
		//asks for password
		if(EConfig::$data['generic']['enabled']=='protected'){
			//asks for password
		}
	}
	
}

?>
