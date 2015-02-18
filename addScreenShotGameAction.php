<?php

include("gfx3/lib.php");

$prevpage = EPageProperties::get_previous_page();

$idcontent = EHeaderDataParser::db_post("idcontent");
$n = EHeaderDataParser::db_post("number");

if(!empty($_FILES['localfile'])){
	$client = new OCSClient();
	$client->set_auth_info(EUser::nick(),EUser::password());
	$client->set_upload_file($_FILES['localfile']['tmp_name']);
	$result = $client->post("v1/content/uploadpreview/$idcontent/$n");
	
	header("Location: $prevpage");
	
}

?>
