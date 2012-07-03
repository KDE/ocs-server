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
		$ext = EFileSystem::get_file_extension($from);
		if(rename($from,$to.".".$ext)){
			return true;
		} else {
			return false;
		}
	}
	
	/*
	 * Get an uploaded file and moves it to $path with $newname
	 */
	public static function move_uploaded_file_in($path,$newname=false){
		$nfile = $_FILES['localfile']['name'];
		$ext = EFileSystem::get_file_extension($nfile);
		if(move_uploaded_file($_FILES['localfile']['tmp_name'], getcwd()."/".$path.$nfile)){
			if($newname){
				EFileSystem::rename_file($path.$nfile,$path.$newname); 
			}
			return true;
		} else {
			return false;
		}
	}
	
	/*
	 * Get an uploaded file and moves it to $path with $newname
	 */
	public static function get_uploaded_file_name(){
		if(isset($_FILES['localfile']['name'])){
			return $_FILES['localfile']['name'];
		}
	}
	
}

?>
