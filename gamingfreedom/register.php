<?php

include("gfx3/lib.php");

$prevpage = EPageProperties::get_previous_page();


$login = EHeaderDataParser::db_post("login");
$password = EHeaderDataParser::db_post("password");
$email = EHeaderDataParser::db_post("email");
$firstname = EHeaderDataParser::db_post("firstname");
$lastname = EHeaderDataParser::db_post("lastname");

$postdata = array(
	"login" => $login,
	"password" => $password,
	"email" => $email,
	"firstname" => $firstname,
	"lastname" => $lastname
	);

$client = new OCSClient(EConfig::$data["ocs"]["host"]);
$check = $client->post("v1/person/add",$postdata);

if($check["ocs"]["meta"]["statuscode"]=="100"){
	
	// add a post variable to be inserted
	EHeaderDataParser::add_post("tgroup","user");
	
	//wrapper
	$users = new EData("ocs_person");
	$users->insert(array("login","password","email","firstname","lastname","tgroup"));
	
	header("Location: $prevpage?e=Registration%20completed!");
	
} else {
	// redirecting to main page
	$message = $check["ocs"]["meta"]["message"];
	$message = str_replace(" ","%20",$message);
	header("Location: $prevpage?e=".$message);
	
}

?>
