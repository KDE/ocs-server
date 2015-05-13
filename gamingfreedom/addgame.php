<?php

include("gfx3/lib.php");

$prevpage = EPageProperties::get_previous_page();

$name = EHeaderDataParser::db_post("name");
$type = EHeaderDataParser::db_post("type");
$downloadname1 = EHeaderDataParser::db_post("downloadname1");
$downloadlink1 = EHeaderDataParser::db_post("downloadlink1");
$description = EHeaderDataParser::db_post("description");
$summary = EHeaderDataParser::db_post("summary");
$version = EHeaderDataParser::db_post("version");
$changelog = EHeaderDataParser::db_post("changelog");
$personid = EUser::nick();

$postdata = array(
	"name" => $name,
	"type" => $type,
	"downloadname1" => $downloadname1,
	"downloadlink1" => $downloadlink1,
	"description" => $description,
	"summary" => $summary,
	"version" => $version,
	"changelog" => $changelog,
	"personid" => $personid
	);

$client = new OCSClient(EConfig::$data["ocs"]["host"]);
$client->set_auth_info(EUser::nick(),EUser::password());
$check = $client->post("v1/content/add",$postdata);

if($check["ocs"]["meta"]["statuscode"]=="100"){
	
	$id = $check["ocs"]["data"]["content"]["id"];
	//nothing hard insert into gamingfreedom database
	$pname = ERewriter::prettify($name);
	header("Location: game.php/title/$pname/id/$id");
	
} else {
echo	$client->get_last_raw_result();
	// redirecting to main page
	$message = $check["ocs"]["meta"]["message"];
	$message = str_replace(" ","%20",$message);
	//header("Location: $prevpage?e=".$message);
}

?>
