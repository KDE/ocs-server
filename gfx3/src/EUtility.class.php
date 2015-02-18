<?php

/*
 *   TRT GFX 3.0.1 (beta build) BackToSlash
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
		return str_replace("<br>", "\r\n", $string);
	}
	
	public static function nl2br($string) {
		$string = str_replace('\r\n', '<br>', $string);
		$string = str_replace('\r', '<br>', $string);
		$string = str_replace('\n', '<br>', $string);
		return $string;
	}
	
}

?>
