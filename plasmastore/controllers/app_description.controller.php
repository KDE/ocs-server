<?php
class App_descriptionController extends EController {
	public function show ($args) {
		$cat = new RetrieveModel();
		EStructure::view("app_description",
		 $cat->getCategories(),
		 $cat->getSingleContentData($args[0]),
		 $cat->getComments($args[0])
		 );
	}
	public function leaveComment ($args) {
		$dat = new CommentsModel();
		$dat->leaveComment($args[0]);
	}	
}
?>
