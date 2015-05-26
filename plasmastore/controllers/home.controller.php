<?php
class HomeController extends EController {
	public function index ($args) {
		$cat = new RetriveModel();
		EStructure::view("category_showcase", $cat->getCategories(), $cat->getData());

	}
}
?>