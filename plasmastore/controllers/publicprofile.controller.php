<?php
class PublicProfileController extends EController {
	
	public function index ($args) {
		$dat2 = new RetrieveModel();
		if (OCSUser::is_logged()) {
			EStructure::view("public_profile", $dat2->getUserInfo($args[0]), $dat2->getUserData(1));
		}
	}
	public function addFriend ($args){
	$friend = new FriendModel();
		if (OCSUser::is_logged()) {
			$friend->sendRequest($args[0]);
		}
	}
}

?>
