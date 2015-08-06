<?php
class CommentsModel extends EModel {
    public function leaveComment ($id) {
        $type = "1";
        $content = $id;
        $content2 = "1";
        $parent = "";
        $subject = EHeaderDataParser::secure_post("inputSubject");
        $message = EHeaderDataParser::secure_post("inputMessage");

        $postdata = array(
            "type" => $type,
            "content" => $content,
            "content2" => $content2,
            "parent" => $parent,
            "subject" => $subject,
            "message" => $message,
            );

        $client = new OCSClient(EConfig::$data["ocs"]["host"]);
        $client->set_auth_info(EHeaderDataParser::get_cookie("login"),EHeaderDataParser::get_cookie("password"));
        $check = $client->post("v1/comments/add",$postdata);

        if($check["ocs"]["meta"]["statuscode"]=="100"){
            //cosa fare se va a buon fine
            header("Location: /plasmastore/app_description/show/$id");
        }
        else {echo $check["ocs"]["meta"]["statuscode"];}
    }
}

?>