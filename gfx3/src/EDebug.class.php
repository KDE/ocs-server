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
 * This module aims to create a set of tools for easy debugging php apps using gfx.
 */

// BREAKPOINT
define("DEBUG_BREAKPOINT", "echo \"<pre>\";
var_dump(get_defined_vars());
echo \"</pre>\"; ");

?>
