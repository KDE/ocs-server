<?php
class CommentsModel extends EModel {
    public function leaveComment ($id) {
        $type = "1";
        $contentid = $id;
        $contentid2 = "1";
        $parent = "";
        $subject = EHeaderDataParser::secure_post("inputSubject");
        $message = EHeaderDataParser::secure_post("inputMessage");

        $postdata = array(
            "type" => $type,
            "contentid" => $contentid,
            "contentid2" => $contentid2,
            "parent" => $parent,
            "subject" => $subject,
            "message" => $message,
            );

        $client = new OCSClient(EConfig::$data["ocs"]["host"]);
        $client->set_auth_info(EHeaderDataParser::get_cookie("login"),EHeaderDataParser::get_cookie("password"));
        $check = $client->post("v1/comments/add",$postdata);

        if($check["ocs"]["meta"]["statuscode"]=="100"){
            
            $id = $check["ocs"]["data"]["content"]["id"];
            //cosa fare se va a buon fine
            header("Location: /plasmastore/app_description/show/$id");
        }
        else {echo "ERROR";}
    }
}

?>