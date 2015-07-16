<?php
class FriendModel extends EModel {
	public function sendRequest($id) { 
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info(OCSUser::$login, EHeaderDataParser::get_cookie("password"));
		$check = $client->post("/v1/friend/invite/",$id);
	}
}

?>
