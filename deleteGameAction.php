<?php

include("gfx3/lib.php");

$idcontent = EHeaderDataParser::db_get("id");

$postdata = array(
	"contentid" => $idcontent
	);

$client = new OCSClient();
$client->set_auth_info(EUser::nick(),EUser::password());
$client->set_post_data($postdata);
$result = $client->post("v1/content/delete/$idcontent");

header("Location: /gamelist.php/user/".EUser::nick());

?>
