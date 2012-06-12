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
class ESlider {
	private $element = 0;
	private $to_slide = 0;
	private $currentpage = "nd";
	
	public function __construct($to_slide){
		if(isset($_GET['element'])){
			$this->element = $_GET['element'];
		} else {
			$this->element = 0;
		}
		$this->to_slide = $to_slide;
		$this->currentpage = $_SERVER['SCRIPT_NAME'];
	}
	
	public function left_arrow($text,$extra=array(),$anchor=""){
		if(!$this->element<=0){
			$c_element = $this->element - $this->to_slide;
			$link = "<a href=\"".$this->currentpage."?element=$c_element";
			if(!empty($extra)){
				foreach($extra as $parameter => $value){
					$link = $link."&$parameter=$value";
				}
			}
			$link = $link.$anchor."\">$text</a>";
			return $link;
		}
	}
	
	public function right_arrow($text,$extra=array(),$anchor=""){
		$c_element = $this->element + $this->to_slide;
		$link = "<a href=\"".$this->currentpage."?element=$c_element";
		if(!empty($extra)){
			foreach($extra as $parameter => $value){
				$link = $link."&$parameter=$value";
			}
		}
		$link = $link.$anchor."\">$text</a>";
		return $link;
	}
	
	public function slide($pure=false){
		return " LIMIT ".$this->element.",".$this->to_slide." ";
		if($pure==true){
			return $this->element.",".$this->to_slide;
		}
	}
	
	public function hidden_field(){
		$string = "<input type=\"hidden\" name=\"element\" value=\"".$this->element."\">";
		return $string;
	}
	
	public function fast_slider($extra=array(),$anchor=""){
		if(empty($extra)){
			return "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
			<tr>
			<td width=\"50%\" align=\"left\">".$this->left_arrow("Pagina Successiva",false)."</td><td width=\"50%\" align=\"right\">".$this->right_arrow("Pagina Precedente",false)."</td>
			</tr>
			</table>";
		} else {
			return "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
			<tr>
			<td width=\"50%\" align=\"left\">".$this->left_arrow("Pagina Successiva",$extra,$anchor)."</td><td width=\"50%\" align=\"right\">".$this->right_arrow("Pagina Precedente",$extra,$anchor)."</td>
			</tr>
			</table>";
		}
	}
}

?>
