<?php

include("gfx3/lib.php");

$prevpage = EPageProperties::get_previous_page();

$login = EHeaderDataParser::db_post("login");
$password = EHeaderDataParser::db_post("password");

$postdata = array(
	"login" => $login,
	"password" => $password
	);

$client = new OCSClient(EConfig::$data["ocs"]["host"]);

$check = $client->post("v1/person/check",$postdata);

//ELog::pd($client->get_last_raw_result());

if($check["ocs"]["meta"]["statuscode"]=="100"){
	EUser::login($login,$password);
	header("Location: $prevpage?e=Logged!");
}



?>
