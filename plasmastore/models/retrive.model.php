<?php
class RetriveModel extends EModel {

public function getCategories() {
	$client = new OCSClient();
	return $client->get("/gamingfreedom.org/v1/content/categories");
}

public function getData() {
	$client = new OCSClient();
	return $client->get("/gamingfreedom.org/v1/content/data");
}
}

?>