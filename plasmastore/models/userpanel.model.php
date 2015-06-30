<?php
class UserPanelModel extends EModel {
    public function upload () {
        $name = EHeaderDataParser::secure_post("inputTitle");
        $type = EHeaderDataParser::secure_post("type");
        $downloadname1 = EHeaderDataParser::secure_post("inputDownloadName");
        $downloadlink1 = EHeaderDataParser::secure_post("inputDownloadLink");
        $description = EHeaderDataParser::secure_post("inputDescription");
        $summary = EHeaderDataParser::secure_post("inputSummary");
        $version = EHeaderDataParser::secure_post("inputVersion");
        $changelog = EHeaderDataParser::secure_post("inputChangelog");
        $personid = EHeaderDataParser::get_cookie("login");

        $postdata = array(
            "name" => $name,
            "type" => $type,
            "downloadname1" => $downloadname1,
            "downloadlink1" => $downloadlink1,
            "description" => $description,
            "summary" => $summary,
            "version" => $version,
            "changelog" => $changelog,
            "personid" => $personid
            );

		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info(EHeaderDataParser::get_cookie("login"),EHeaderDataParser::get_cookie("password"));
		$check = $client->post("v1/content/add",$postdata);
		 //print_r($_FILES);
		 //print_r($_FILES['localfile']['tmp_name']);

		if($check["ocs"]["meta"]["statuscode"]=="100"){
			
			$id = $check["ocs"]["data"]["content"][0]["id"];
			
			$client = new OCSClient(EConfig::$data["ocs"]["host"]);
			$client->set_auth_info(EHeaderDataParser::get_cookie("login"),EHeaderDataParser::get_cookie("password"));
			$client->set_upload_file($_FILES['localfile']['tmp_name']);
			$result = $client->post("v1/content/uploadpreview/$id/1");
			if($result["ocs"]["meta"]["statuscode"]=="100"){
				//cosa fare se va a buon fine
				header("Location: /plasmastore/app_description/show/$id/$name");
			} else {ELog::pd($result);}
		}
	}

    public function edit ($args) {
        $id=$args;
        $name = EHeaderDataParser::secure_post("inputTitle");
        $type = EHeaderDataParser::secure_post("type");
        $downloadname1 = EHeaderDataParser::secure_post("inputDownloadName");
        $downloadlink1 = EHeaderDataParser::secure_post("inputDownloadLink");
        $description = EHeaderDataParser::secure_post("inputDescription");
        $summary = EHeaderDataParser::secure_post("inputSummary");
        $version = EHeaderDataParser::secure_post("inputVersion");
        $changelog = EHeaderDataParser::secure_post("inputChangelog");
        $personid = EHeaderDataParser::get_cookie("login");

        $postdata = array(
            "name" => $name,
            "type" => $type,
            "downloadname1" => $downloadname1,
            "downloadlink1" => $downloadlink1,
            "description" => $description,
            "summary" => $summary,
            "version" => $version,
            "changelog" => $changelog,
            "personid" => $personid
            );

    $client = new OCSClient(EConfig::$data["ocs"]["host"]);
    $client->set_auth_info(EHeaderDataParser::get_cookie("login"),EHeaderDataParser::get_cookie("password"));
    $check = $client->post("v1/content/edit/$id",$postdata);

    if($check["ocs"]["meta"]["statuscode"]=="100"){
        
        $id = $check["ocs"]["data"]["content"]["id"];
        //cosa fare se va a buon fine
        header("Location: /plasmastore/app_description/show/$id");
    }
}
    public function getUserData() {
    $user = new OCSClient;
    $pw = $_COOKIE["password"];
    $name = $_COOKIE["login"];
    $user->set_auth_info($name, $pw);
    return $user->get ("v1/person/self");
    }
} 
?>
