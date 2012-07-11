<?php
include_once("../gfx3/lib.php");

$main = new EMain();
$temp = new EStructure();

$temp->code();
	echo "<p style=\"color:green;\">Working!</p>";
$temp->insert("corelibs");


$temp->code();
$db = new EDatabase();
	if($db->status()==0){
		echo "<p style=\"color:green;\">Working!</p>";
	} elseif($db->status()==1) {
		echo "<p style=\"color:red;\">No database found!</p>";
	} else {
		echo "<p style=\"color:red;\">Connection refused!</p>";
	}
$temp->insert("database");


$temp->code();
	echo "<p style=\"color:green;\">Working!</p>";
$temp->insert("templates");

?>
