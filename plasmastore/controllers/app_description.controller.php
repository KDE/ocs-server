<?php
class App_descriptionController extends EController {
	private static $id;
	public function show ($args) {
		$cat = new RetrieveModel();
		EStructure::view("app_description",
		 $cat->getCategories(),
		 $cat->getSingleContentData($args[0]),
		 $cat->getComments($args[0])
		 );
		self::$id=$args[0];
	}
	public function leaveComment () {
		$dat = new CommentsModel();
		$dat->leaveComment(self::$id);
	}	
}
?>
