<?php
class RetrieveModel extends EModel {
//if i use this model also to delete data i should consider to change its name
public function getCategories() { 
	$client = new OCSClient();
	return $client->get("v1/content/categories");
}

public function getData() {
	$client = new OCSClient();
	return $client->get("v1/content/data/?pagesize=1");
}

public function getDataPerPage($pagenumber) {
	$client = new OCSClient();
	return $client->get("v1/content/data/?pagesize=1&page=$pagenumber");
}

public function delData($id) {
	$client = new OCSClient();
	$client->set_auth_info(EHeaderDataParser::get_cookie("login"),EHeaderDataParser::get_cookie("password"));
	$client->post("v1/content/delete/$id");
	$prevpage = EPageProperties::get_previous_page();
        header("Location: $prevpage");
}
public function getSingleContentData($id) {
	$client = new OCSClient();
	return $client->get("v1/content/data/$id");
}
public function getComments($id) {
	$client = new OCSClient();
	return $client->get("v1/comments/data/1/$id/1");
}
public function getUserInfo() {
	$user = new OCSClient;
	$pw = $_COOKIE["password"];
	$name = $_COOKIE["login"];
	$user->set_auth_info($name, $pw);
	return $user->get ("v1/person/self");
}

public function getUserData($pagenumber) {
	$user = new OCSClient;
	$name = $_COOKIE["login"];
	return $user->get("v1/content/data/?user=$name&page=$pagenumber");
}

}

?>
