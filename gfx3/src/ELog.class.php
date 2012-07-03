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
 * FIXME: Aims to replace current "die()" error system.
 */

class ELog {
	/*
	 * mode:
	 *  0 - print log on screen in plain text
	 *  1 - print log on screen with some html
	 *  2 - print log on text file ($name.log)
	 */
	private $errormode = 0;
	private $error_file;
	private $warning_file;
	private $preserve_errors = false;
	private $preserve_warnings = false;
	
	public function __construct($mode=0, $preserve_errors=false, $preserve_warnings=false, $error_file="error.log", $warning_file="warning.log"){
		$this->mode = $mode;
		$this->error_file = $error_file;
		$this->warning_file = $warning_file;
		$this->preserve_errors = $preserve_errors;
		$this->preserve_warnings = $preserve_warnings;
	}
	
	/*
	 * various setters
	 */
	public function set_preserve_errors($b){ $this->preserve_errors = $b; }
	public function set_preserve_warnings($b){ $this->preserve_warning = $b; }
	public function set_preserve($b){ $this->preserve = $b; }
	public function set_error_file($s){ $this->error_file = $s; }
	public function set_warning_file($s){ $this->warning_file = $s; }
	public function set_mode($i){ $this->mode = $i; }
	
	public function error($r){
		switch ($this->mode){
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
				$stream = fopen($this->error_file, 'a+');
				fwrite($stream, "GFX ERROR: $r\n\n");
				fclose($stream);
		}
	}
	
	public function warning($r){
		switch ($this->mode){
			case 0:
				echo "GFX WARNING: $r<br>";
				break;
			case 1:
				echo "<p style=\"color:#CDD500;font-size:20px;\">GFX WARNING:</p>
					<p style=\"color:#D58600;font-size:15px;font-style:italic;\">$r</p>";
			case 2:
				$stream = fopen($this->warning_file, 'a+');
				fwrite($stream, "GFX WARNING: $r\n\n");
				fclose($stream);
		}
	}
	
	public function clear_log(){
		if(file_exists($error_file) and $this->preserve_errors == false){
			unlink($error_file);
		}
		if(file_exists($warning_file) and $this->preserve_warnings == false){
			unlink($warning_file);
		}
	}
	
}

?>
