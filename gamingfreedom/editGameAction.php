<?php

include("gfx3/lib.php");

$prevpage = EPageProperties::get_previous_page();

$id = EHeaderDataParser::db_post("idcontent");
$name = EHeaderDataParser::db_post("name");
$downloadname1 = EHeaderDataParser::db_post("downloadname1");
$downloadlink1 = EHeaderDataParser::db_post("downloadlink1");
$description = EUtility::nl2br(EHeaderDataParser::db_post("description"));
$version = EHeaderDataParser::db_post("version");
$summary = EHeaderDataParser::db_post("summary");
$changelog = nl2br(EHeaderDataParser::db_post("changelog"));

$postdata = array(
	"name" => $name,
	"summary" => $summary,
	"downloadname1" => $downloadname1,
	"downloadlink1" => $downloadlink1,
	"description" => $description,
	"version" => $version,
	"changelog" => $changelog
	);

$client = new OCSClient();
$client->set_auth_info(EUser::nick(),EUser::password());
$mod = $client->post("v1/content/edit/$id",$postdata);

if($mod["ocs"]["meta"]["statuscode"]=="100"){
	header("Location: $prevpage");
} else {
	ELog::pd($mod);
	die();
	header("Location: $prevpage");
}

?>
