<?php
class FriendModel extends EModel {
	public function sendRequest($id) { 
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info(OCSUser::login(), EHeaderDataParser::get_cookie("password"));
		$message="aaaa";
		$postdata = array(
		 "message"=>$message
		 );
		return $check = $client->post("/v1/friend/invite/$id", $postdata);
	}
	public function listFriends($id){
		$client = new OCSClient();
		$self = new OCSUser();
		$client->set_auth_info($self->login(), EHeaderDataParser::get_cookie("password"));
		return $client->get("/v1/friend/data/$id");
	}
}

?>
