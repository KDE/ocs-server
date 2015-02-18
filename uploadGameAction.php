<?php

include("gfx3/lib.php");

$prevpage = EPageProperties::get_previous_page();

$idcontent = EHeaderDataParser::db_post("idcontent");

if(!empty($_FILES['localfile'])){
	
	$tmp_name = $_FILES['localfile']['tmp_name'];
	$name = $_FILES['localfile']['name'];
    
	$client = new OCSClient();
	$client->set_auth_info(EUser::nick(),EUser::password());
	$abs_name = EFileSystem::rename_file($tmp_name,"/tmp/".$name);
	$client->set_upload_file("/tmp/".$name);
	$result = $client->post("v1/content/uploaddownload/$idcontent");
	if($result["ocs"]["meta"]["statuscode"]=="100"){
		header("Location: $prevpage");
	} else {
        //echo $client->get_last_raw_result();
		ELog::error("something went wrong");
	}
	
}

?>
