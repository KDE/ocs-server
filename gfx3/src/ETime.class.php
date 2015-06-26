<?php

/*
 *   GFX 4
 * 
 *   support:	happy.snizzo@gmail.com
 *   website:	http://www.gfx3.org
 *   credits:	Claudio Desideri
 *   
 *   This software is released under the MIT License.
 *   http://opensource.org/licenses/mit-license.php
 */ 

/*
 * Class used to do time measurement.
 */
class ETime {
	
	private static $time_start;
	
	public static function measure_from(){
		ETime::$time_start = microtime(true);
	}
	
	public static function measure_to() { 
		$time_end = microtime(true);
		$time = $time_end - ETime::$time_start;
		return $time;
	}
	
}

?>
