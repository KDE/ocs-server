<?php

/*
 *   GFX 4
 * 
 *   support:	happy.snizzo@gmail.com
 *   website:	http://gfx-framework.googlecode.com
 *   credits:	Claudio Desideri
 *   
 *   This software is released under the MIT License.
 *   http://opensource.org/licenses/mit-license.php
 */ 

/*
 * This class is intended to be extended for using as generic controller.
 */

class EController {
	
	private $args;
	
	public function __construct(){
		$this->args = array();
	}
	
	public function set_args($args){
		$this->args = $args;
	}
	
	/*
	 * Returns the args of the controller in position $p
	 * if arg is not defined or empty, return false
	 */
	public function arg_pos($p){
		$p -= 1;
		if(isset($this->args[$p]) and !empty($this->args[$p])){
			return $this->args[$p];
		} else {
			return false;
		}
	}
	
	/*
	 * Check if the arg with name $n exists in the stacks
	 */
	public function arg_key($p){
		if(in_array($p, $this->args)){
			return true;
		} else {
			return false;
		}
	}
}

?>
