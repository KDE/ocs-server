<?php

include("gfx3/lib.php");

$prevpage = EPageProperties::get_previous_page();

$type = EHeaderDataParser::db_post("type");
$content = EHeaderDataParser::db_post("content");
$content2 = EHeaderDataParser::db_post("content2");
$parent = EHeaderDataParser::db_post("parent");
$subject = EHeaderDataParser::db_post("subject");
$message = EHeaderDataParser::db_post("message");

$postdata = array(
	"type" => $type,
	"content" => $content,
	"content2" => $content2,
	"parent" => $parent,
	"subject" => $subject,
	"message" => $message
	);

$client = new OCSClient();
$client->set_auth_info(EUser::nick(),EUser::password());
$client->set_post_data($postdata);
$res = $client->post("v1/comments/add");

if($res["ocs"]["meta"]["statuscode"]=="100"){
	header("Location: game.php/id/$content");
} else {
	ELog::pd($res);
}

?>
