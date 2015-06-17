<?php
class UserPanelController extends EController {
	public function index ($args) {
		$dat = new RetrieveModel();
		if (isset($_COOKIE["login"])) {
			EStructure::view("user_control_panel", $dat->getUserData());
		}
		else {echo "ERROR: You're not logged in";}
	}

}

?>
