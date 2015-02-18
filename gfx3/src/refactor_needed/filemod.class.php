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
 * TODO: This class needs to be refactured.
 * uploaded files are mixed with images.
 * 
 * Need to create a EFile class, maybe to be extended.
 */

class EFileMod {
	
	/*
	 * Function that return file extension in string format if exists,
	 * return false in the opposite case
	 */
	public function get_file_extension($nome){
		$trova_punto = explode(".", $nome);
		$estensione = $trova_punto[count($trova_punto) - 1];
		$estensione = strtolower($estensione);
		if (isset($trova_punto[1]) == FALSE){
			return false;
		}
		return $estensione;
	}
	
	/*
	 * Quick and dirty upload form.
	 */
	
	public function form_upload($action,$filesize){
		echo "<form enctype=\"multipart/form-data\" action=\"$action\" method=\"POST\">
		Scegli il file:
		<input name=\"uploadedfile\" type=\"file\" /><br />
		<input type=\"submit\" value=\"carica file\" />
		</form>";
	}
	
	/*
	 * Function that renames a file, mantaining the correct extension
	 */
	public function rename_file($from,$to){
		$ext = $this->get_file_extension($from);
		if(rename($from,$to.".".$ext)){
			return true;
		} else {
			return false;
		}
	}
	
	/*
	 * Get an uploaded file and moves it to $path with $newname
	 */
	public function get_uploaded_file($path,$newname=false){
		$nfoto = $_FILES['uploadedfile']['name'];
		$ext = $this->get_file_extension($nfoto);
		if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $path.$nfoto)){
			if($newname){
				$this->rename_file($path.$nfoto,$newname);
			}
		}
	}
	
	/*
	 * Makes a thumbnail mantaining the aspect ratio.
	 * That's what $maxwidth and $maxheight are for.
	 * 
	 * WARNING: works only with JPG or PNG
	 */
	public function make_thumbnail($from,$to,$maxwidth,$maxheight){
		$ext = $this->get_file_extension($from);
		if(!list($width, $height, $type, $attr) = getimagesize($from)){ echo "error: $from |"; }
		if($width>$height){
			$x = $width/$maxwidth;
			$fwidth = floor($width / $x);
			$fheight= floor($height / $x);
		}
		if($width<$height){
			$x = $height/$maxheight;
			$fheight = floor($height / $x);
			$fwidth= floor($width / $x);
		}
		if($width==$height){
			if($maxwidth>$maxheight){ $max = $maxheight; } else { $max = $maxwidth; }
			$x = $height/$max;
			$fheight = floor($height / $x);
			$fwidth = floor($width / $x);
		}
		$thumb = imagecreatetruecolor($fwidth, $fheight);
		if($ext=="jpg" or $ext=="jpeg"){$source = imagecreatefromjpeg($from);}
		if($ext=="png"){$source = imagecreatefrompng($from);}
		imagecopyresized($thumb, $source, 0, 0, 0, 0, $fwidth, $fheight, $width, $height);
		if($ext=="jpg" or $ext=="jpeg"){imagejpeg($thumb, $to, 100);}
		if($ext=="png"){imagepng($thumb, $to, 1);}
		if($ext!="jpg" or $ext!="jpeg" or $ext!="png"){
			return false;
		} else {
			return true;
		}
	}
}

?>
