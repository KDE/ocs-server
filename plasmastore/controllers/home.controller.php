<?php
class HomeController extends EController {
	public function index ($args) {
		$cat = new RetrieveModel();
		EStructure::view("category_showcase", $cat->getCategories(), $cat->getData());

	}
	public function delData ($args) {
		$dat = new RetrieveModel(); 
		$dat->delData($args[0]);
	}

	public function page ($args) {
		$pag = new RetrieveModel();
		EStructure::view("category_showcase", $pag->getCategories(), $pag->getDataPerPage($args[0]));
	}
}
?>
