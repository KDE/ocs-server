<?php
class LoginController extends EController {
	public function login(){ 
        $loginModel = new LoginModel();
        $login = EHeaderDataParser::db_post("login");
    	$password = EHeaderDataParser::db_post("password");
    
    if($loginModel->login($login, $password)){
        header("Location: $prevpage?e=Logged!");
        } else {
    header("Location: $prevpage?e=Error!");
    }
        
    }

	}
?>
