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
 * TODO: Needs refactor
 */

class EStructure {
	
	private $current_token = 0;
	private $page;
	private $templatePage;
	private $moduleStack = array();
	private $cwd;
	
	public function __construct($select="main"){
		$this->cwd = getcwd();
		$this->templatePage = $select;
	}
	
	//include modules
	public function module($fn, $place, $preserve=true){
		$mdAdd = array('filename' => $fn,
				'place' => $place,
				'preserve' => $preserve);
		$this->moduleStack[] = $mdAdd;
	}
	
	public function code(){
		ob_start();
	}
	
	public function insert($place, $preserve=true){
		$output = ob_get_clean();
		$mdAdd = array('filename' => 'code',
				'place' => $place,
				'preserve' => $preserve,
				'output' => $output);
		$this->moduleStack[] = $mdAdd;
	}
	
	public function __destruct(){
		chdir($this->cwd);
		$template = file_get_contents("template/".$this->templatePage.".html");
		$template_token = explode("--^--", $template);
		for($i=$this->current_token;$i<count($template_token);$i++){
			if(($i%2!=0)){
				if(is_file("template/".$template_token[$i].".php")){
					ob_start();
					include("template/".$template_token[$i].".php");
					$output = ob_get_clean();
					$template = str_replace("<!--^--".$template_token[$i]."--^-->", $output, $template);
				}
			}
		}
		$this->page = $template;
		
		foreach($this->moduleStack as $module){
			$fn = $module['filename'];
			$place = $module['place'];
			$preserve = $module['preserve'];
			
			if($fn=="code"){
				$template = $this->page;
				$output = $module['output'];
				if($preserve){
					$str = $output."<!--^--".$place."--^-->";
				} else {
					$str = $output;
				}
				$template = str_replace("<!--^--".$place."--^-->", $str, $template);
				$this->page = $template;
			} else {
				if(is_file("modules/".$fn.".php")){
					$template = $this->page;
					ob_start();
					include("modules/".$fn.".php");
					$output = ob_get_clean();
					if($preserve){
						$str = $output."<!--^--".$place."--^-->";
					} else {
						$str = $output;
					}
					$template = str_replace("<!--^--".$place."--^-->", $str, $template);
					$this->page = $template;
				} else {
					die("TRT GFX ISSUE: module <b>$fn</b> not found in modules/$fn.php");
				}
			}
		}
		
		$template = $this->page;
		echo $template;
	}
	
	//TODO: use EError class!
	public function error_box($error){
		$this->page = "<div style=\"border:3px red solid;-moz-border-radius:10px;background-color:#CECECE;padding:7px;margin:auto;margin-top:7px;margin-bottom:7px;font-size:100%; width:300px;\">
		<center><big><big>Ci scusiamo per l'inconveniente!</big></big></center>
		$error</div>";
		die();
	}
}

?>
