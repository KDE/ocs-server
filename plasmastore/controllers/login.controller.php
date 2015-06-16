<?php
class LoginController extends EController {
	public function logout() {
        OCSUser::client_logout();
        $prevpage = EPageProperties::get_previous_page();
        header("Location: $prevpage");
    }

 	public function prevpage() {
 		$prevpage = EPageProperties::get_previous_page();
        header("Location: $prevpage");
 	}
 }

	
?>
