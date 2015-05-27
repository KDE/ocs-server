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

}

?>