<?php
class RetrieveModel extends EModel {

public function getCategories() {
	$client = new OCSClient();
	return $client->get("v1/content/categories");
}

public function getData() {
	$client = new OCSClient();
	return $client->get("v1/content/data");
}
public function getSingleContentData($id) {
	$client = new OCSClient();
	return $client->get("v1/content/data/$id");
}
public function getComments($id) {
	$client = new OCSClient();
	return $client->get("v1/comments/data/1/$id/1");
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