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

class EImage {
	
	/*
	 * To be defined.
	 */
	public function __construct(){
		//
	}
	
	/*
	 * Makes a thumbnail mantaining the aspect ratio.
	 * That's what $maxwidth and $maxheight are for.
	 * 
	 * WARNING: works only with JPG or PNG
	 */
	public function make_thumbnail($from,$to,$maxwidth,$maxheight){
		$ext = EFileSystem::get_file_extension($from);
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
