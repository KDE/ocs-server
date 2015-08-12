<?php
class PublicProfileController extends EController {
	
	public function index ($args) {
		$dat = new RetrieveModel();
		$friend = new FriendModel;
		$self = new OCSUser;
		if (OCSUser::is_logged()) {
			EStructure::view("public_profile", $dat->getUserInfo($args[0]), $dat->getUserData(1), $friend->listFriends($self->login()));
		}
	}
	
	public function addfriend ($args){
		$friend = new FriendModel();
		if (OCSUser::is_logged()) {
			var_dump($friend->sendRequest($args[0]));
		}
	}
}

?>
