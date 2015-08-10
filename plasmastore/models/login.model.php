<?php
class LoginModel extends EModel {
    
    public function login($nick, $pass){
    $postdata = array(
        "login" => $login,
        "password" => $password
        );
    $client = new OCSClient(EConfig::$data["ocs"]["host"]);
    $check = $client->post("v1/person/check",$postdata);
    //ELog::pd($client->get_last_raw_result());
    if($check["ocs"]["meta"]["statuscode"]=="100"){
        EUser::login($login,$password);
        return true;
} else {
    return false; //TODO: provide an error message
}
}
}