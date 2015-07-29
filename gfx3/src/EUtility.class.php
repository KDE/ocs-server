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
	
	public static function redirect($page)
	{
		header('location: $page');
	}
	
	/*
	 * TODO DOC
	 */
	public static function hide_output()
	{
		ob_start();
	}
	
	public static function show_output()
	{
		return ob_get_clean();
	}
	
	/**
	 * DEPRECATED
	 * moved to EPageProperties
	 * kept this for retrocompatibility
	 * use EPageProperties
	 */
	public static function get_domain($domain, $debug = false)
	{
		return EPageProperties::get_domain($domain, $debug);
	}
	
	/**
	 * DEPRECATED
	 * moved to EPageProperties
	 * kept this for retrocompatibility
	 * use EPageProperties
	 */
	public static function get_clear_domain($domain)
	{
		return EPageProperties::get_clear_domain($domain);
	}

	
}

?>
