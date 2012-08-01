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
 *
 */

class EStructure {
	
	private static $current_token = 0;
	private static $page;
	private static $templatePage;
	private static $moduleStack = array();
	private static $cwd;
	
	public static function load($select="main"){
		EStructure::$cwd = getcwd();
		EStructure::$templatePage = $select;
	}
	
	//include modules
	public static function module($fn, $place, $preserve=true){
		$mdAdd = array('filename' => $fn,
				'place' => $place,
				'preserve' => $preserve);
		EStructure::$moduleStack[] = $mdAdd;
	}
	
	public static function code(){
		ob_start();
	}
	
	public static function insert($place, $preserve=true){
		$output = ob_get_clean();
		$mdAdd = array('filename' => 'code',
				'place' => $place,
				'preserve' => $preserve,
				'output' => $output);
		EStructure::$moduleStack[] = $mdAdd;
	}
	
	public static function unload(){
		chdir(EStructure::$cwd);
		$template = file_get_contents("template/".EStructure::$templatePage.".html");
		$template_token = explode("--^--", $template);
		for($i=EStructure::$current_token;$i<count($template_token);$i++){
			if(($i%2!=0)){
				if(is_file("template/".$template_token[$i].".php")){
					ob_start();
					include("template/".$template_token[$i].".php");
					$output = ob_get_clean();
					$template = str_replace("<!--^--".$template_token[$i]."--^-->", $output, $template);
				}
			}
		}
		EStructure::$page = $template;
		
		foreach(EStructure::$moduleStack as $module){
			$fn = $module['filename'];
			$place = $module['place'];
			$preserve = $module['preserve'];
			
			if($fn=="code"){
				$template = EStructure::$page;
				$output = $module['output'];
				if($preserve){
					$str = $output."<!--^--".$place."--^-->";
				} else {
					$str = $output;
				}
				$template = str_replace("<!--^--".$place."--^-->", $str, $template);
				EStructure::$page = $template;
			} else {
				if(is_file("modules/".$fn.".php")){
					$template = EStructure::$page;
					ob_start();
					include("modules/".$fn.".php");
					$output = ob_get_clean();
					if($preserve){
						$str = $output."<!--^--".$place."--^-->";
					} else {
						$str = $output;
					}
					$template = str_replace("<!--^--".$place."--^-->", $str, $template);
					EStructure::$page = $template;
				} else {
					die("TRT GFX ISSUE: module <b>$fn</b> not found in modules/$fn.php");
				}
			}
		}
		
		$template = EStructure::$page;
		echo $template;
	}
}

?>
