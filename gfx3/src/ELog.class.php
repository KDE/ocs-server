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
 * Catching and management error class.
 * 
 * Now static class.
 */

class ELog {
	/*
	 * mode:
	 *  0 - print log on screen in plain text
	 *  1 - print log on screen with some html
	 *  2 - print log on text file ($name.log)
	 */
	public static $errormode = 0;
	public static $error_file = "error.log";
	public static $warning_file = "warning.log";
	public static $preserve_errors = false;
	public static $preserve_warnings = false;
	public static $mode = 0;
	
	public static function error($r){
		switch (ELog::$mode){
			case 0:
				echo "GFX ERROR: $r<br><pre> BACKTRACE: ";
				debug_print_backtrace();
				echo "</pre>";
				die();
			case 1:
				die("<div style=\"border:3px red solid;-moz-border-radius:10px;background-color:#CECECE;padding:7px;margin:auto;margin-top:7px;margin-bottom:7px;font-size:100%; width:300px;\">
				<center><big><big><b>GFX ERROR:</b></big></big></center>
				<i>$r</i></div>");
			case 2:
				$stream = fopen(ELog::$error_file, 'a+');
				fwrite($stream, "GFX ERROR: $r\n\n");
				fclose($stream);
		}
	}
	
	public static function warning($r){
		switch (ELog::$mode){
			case 0:
				echo "GFX WARNING: $r<br>";
				break;
			case 1:
				echo "<p style=\"color:#CDD500;font-size:20px;\">GFX WARNING:</p>
					<p style=\"color:#D58600;font-size:15px;font-style:italic;\">$r</p>";
			case 2:
				$stream = fopen(ELog::$warning_file, 'a+');
				fwrite($stream, "GFX WARNING: $r\n\n");
				fclose($stream);
		}
	}
	
	public static function clear_log(){
		if(file_exists(ELog::$error_file) and ELog::$preserve_errors == false){
			unlink(ELog::$error_file);
		}
		if(file_exists(ELog::$warning_file) and ELog::$preserve_warnings == false){
			unlink(ELog::$warning_file);
		}
	}
	
}

?>
