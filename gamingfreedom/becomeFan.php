<?php

include("gfx3/lib.php");

$prevpage = EPageProperties::get_previous_page();

$contentid = EHeaderDataParser::db_get("id");
$postdata = array("idcontent"=>$contentid);
$client = new OCSClient();
$client->set_auth_info(EUser::nick(),EUser::password());
$client->set_post_data($postdata);
$addfan = $client->post("v1/fan/add/$contentid");

if($addfan["ocs"]["meta"]["statuscode"]=="100"){
	header("Location: $prevpage");
} else {
	ELog::pd($addfan);
}

?>
