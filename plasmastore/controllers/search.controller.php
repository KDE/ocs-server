<?php
class SearchController extends EController {
	public function search ($args) {
		$cat = new RetrieveModel();
		$dat = new SearchModel();
		EStructure::view("search", $cat->getCategories(), $dat->search());

	}
}
?>
