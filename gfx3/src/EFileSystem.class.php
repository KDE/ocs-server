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
 *  Static class used as a helper for filesystem operations.
 */
 
class EFileSystem{
	
	/*
	 * Function that return file extension in string format if exists,
	 * return false in the opposite case
	 */
	public static function get_file_extension($nome){
		$trova_punto = explode(".", $nome);
		$estensione = $trova_punto[count($trova_punto) - 1];
		$estensione = strtolower($estensione);
		if (isset($trova_punto[1]) == FALSE){
			return false;
		}
		return $estensione;
	}
	
	/*
	 * Function that renames a file, mantaining the correct extension
	 */
	public static function rename_file($from,$to){
		$ext = $this->get_file_extension($from);
		if(rename($from,$to.".".$ext)){
			return true;
		} else {
			return false;
		}
	}
	
}

?>
