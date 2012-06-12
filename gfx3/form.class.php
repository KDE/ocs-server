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
 * TODO: This class' usefulness needs to be seriously inspected
 *       and decide if rip it away or refactor completely.
 */
class EForm{
	private $target = false;
	private $method = false;
	
	public function open_form($target){
		echo "<form action=\"$target\" method=\"post\"></form>";
	}
	public function text_field($name, $value, $other=""){
		echo "<input class=\"text_field\" type=\"text\" name=\"$name\" value=\"$value\" $other>";
	}
	public function pass_field($name, $value, $other=""){
		echo "<input class=\"text_field\" type=\"password\" name=\"$name\" value=\"$value\" $other>";
	}
	public function submit_button($value, $other=""){
		echo "<input class=\"text_field\" type=\"submit\" value=\"$value\" $other>";
	}
	public function reset_button($value, $other=""){
		echo "<input class=\"text_field\" type=\"reset\" value=\"$value\" $other>";
	}
	public function login_form($target, $other=""){
		echo "<table border=\"0\" class=\"whitebox\">
				<form method=\"post\" action=\"$target\">
					<tr>
						<tr><td>nickname:</td><td><input class=\"text_field\" type=\"text\" name=\"nick\"></td></tr>
						<tr><td>password:</td><td><input class=\"text_field\" type=\"password\" name=\"pass\"></td></tr>
						<tr><td></td><td align=\"right\"><input type=\"submit\" value=\"login\"></td></tr>
					</tr>
					</form>
				</table>";
	}
	public function tinymce($rows, $cols, $value=""){
		echo "<textarea id=\"elm1\" name=\"elm1\" rows=\"$rows\" cols=\"$cols\">".$value."</textarea>";
	}
	public function close_form(){
		echo "</form>";
	}
}

?>
