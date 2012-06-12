<?php
include_once("../main.class.php");

$main = new EMain("pure");
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


$temp->code();
	$user = new EUser();
	$user->getStatus();
	if($user->status()==0){
		echo "<p style=\"color:green;\">Working!</p>";
	} else {
		echo "<p style=\"color:red;\">Database problems!
		<a href=\"#\">Fix this</a></p>";
	}
$temp->insert("users");


$temp->code();
	//FIXME: improve ECache tests
	$cache = new ECache();
	if(!empty($cache)){
		echo "<p style=\"color:green;\">Working!</p>";
	} else {
		echo "<p style=\"color:red;\">Broken!</p>";
	}
$temp->insert("cache");


$temp->code();
	//FIXME: fix permission test.
	if(!empty($cache)){
		echo "<p style=\"color:green;\">Working!</p>";
	}
$temp->insert("permissions");

?>
