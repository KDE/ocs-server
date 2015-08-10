<?php
class UserPanelController extends EController {
	
	public function index ($args) {
		$dat2 = new RetrieveModel();
		if (isset($_COOKIE["login"])) {
			EStructure::view("user_control_panel", $dat2->getSelfInfo(), $dat2->getUserData(1), $dat2->getCategories());
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
		public function page ($args) {
		$dat2 = new RetrieveModel();
		$pag = new RetrieveModel();
		EStructure::view("user_control_panel", $dat2->getSelfInfo(), $dat2->getUserData($args[0]), $dat2->getCategories());
	}
}

?>
