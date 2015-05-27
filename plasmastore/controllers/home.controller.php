<?php
class HomeController extends EController {
	public function index ($args) {
		$cat = new RetrieveModel();
		EStructure::view("category_showcase", $cat->getCategories(), $cat->getData());

	}
}
?>
