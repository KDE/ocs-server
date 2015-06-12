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

class OCSFan {
	
	public function __construct(){
		//
	}
	
	public function add($content){
		$person = OCSUser::id();
		EDatabase::q("INSERT INTO ocs_fan (person,content) VALUES ($person,$content)");
		
		//part needed for activity
		$con = new OCSContent();
		$con->load($content);
		OCSActivity::add(OCSUser::id(), 9, OCSUser::login()." is now fan of ".$con->name);
	}
	
	public function remove($content){
		$person = OCSUser::id();
		EDatabase::q("DELETE FROM ocs_fan WHERE person=$person and content=$content");
		
		//part needed for activity
        $con = new OCSContent();
        $con->load($content);
        OCSActivity::add(OCSUser::id(), 10, OCSUser::login()." is no longer fan of ".$con->name);
	}
	
	public function isfan($content){
		$fant = new EData("ocs_fan");
		
		$person = OCSUser::id();
		$r = $fant->find("*", "where person=$person and content=$content");
		if(!empty($r)){
			return true;
		} else {
			return false;
		}
	}
	
}

?>
