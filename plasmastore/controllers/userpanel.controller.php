<?php
class UserPanelController extends EController {
	public function index ($args) {
		$dat2 = new RetrieveModel();
		if (isset($_COOKIE["login"])) {
			EStructure::view("user_control_panel", $dat2->getUserInfo(), $dat2->getUserData());
		}
		else {echo "ERROR: You're not logged in";}
	}
public function upload() {
	$dat = new UserpanelModel();
	$dat->upload();
}
public function edit($args) {
	$dat = new UserpanelModel();
	$dat->edit($args[0]);
}
}

?>
