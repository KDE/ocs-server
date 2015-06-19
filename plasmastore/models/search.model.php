<?php
class SearchModel extends EModel {

	public function search() {
		$client = new OCSClient();
		$key=EHeaderDataParser::secure_post("searchInput");
		return $client->get("v1/content/data/?search=$key");
	}
}
