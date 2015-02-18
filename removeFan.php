<?php

include("gfx3/lib.php");

$prevpage = EPageProperties::get_previous_page();

$contentid = EHeaderDataParser::db_get("id");

$client = new OCSClient();
$client->set_auth_info(EUser::nick(),EUser::password());
$addfan = $client->post("v1/fan/remove/$contentid");

ELog::pd($client->get_last_raw_result());

if($addfan["ocs"]["meta"]["statuscode"]=="100"){
	header("Location: $prevpage");
} else {
	ELog::pd($addfan);
}

?>
