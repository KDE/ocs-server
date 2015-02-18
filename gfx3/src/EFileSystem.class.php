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
	 * Function that return file name in string format if exists,
	 * return false in the opposite case.
	 * 
	 * It actually erases every extension found.
	 * filename.inc.conf --> filename
	 */
	public static function get_file_name($nome){
		$chunks = explode(".", $nome);
		if (isset($chunks[0]) == FALSE){
			return false;
		} else {
			return $chunks[0];
		}
	}
	
	/*
	 * Function that renames a file, mantaining the correct extension
	 */
	public static function rename_file($from,$to){
		if(rename($from,$to)){
			return true;
		} else {
			return false;
		}
	}
	
	public static function rename_file_ext($from,$to){
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
	public static function move_uploaded_file_in_ext($path,$newname=false){
		$nfile = $_FILES['localfile']['name'];
		if(move_uploaded_file($_FILES['localfile']['tmp_name'], getcwd()."/".$path.$nfile)){
			if($newname){
				EFileSystem::rename_file_ext($path.$nfile,$path.$newname);
			}
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
	
	public static function rmdir($path){
		if(is_dir($path)){
			EFileSystem::clean_all_files_in($path);
			rmdir($path);
		}
	}
	
	/*
	 * erase all files from a folder, except those given in array
	 */
	public static function clean_all_files_in($path="",$safe=""){
		if(empty($safe)){
			$safe = array();
		}
		
		
		if(!empty($path)){
			$prevpath = getcwd();
			if(is_dir($path)){
				chdir($path);
				
				foreach(glob("*") as $filename){
					if(is_file($filename)){
						if(in_array($filename,$safe)){
							continue;
						} else {
							unlink($filename);
							continue;
						}
					}
				}
				
				chdir($prevpath);
			}
		}
	}
	
}

?>
