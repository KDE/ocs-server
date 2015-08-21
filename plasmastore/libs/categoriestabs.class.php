<?php
class CategoriesTabs {
	private $OCSCall="v1/content/categories";
	private $currentCategory = "null";

	public function __construct() {
		$url=$_SERVER['REQUEST_URI'];
		$tokens = explode('/', $url);
		if ($tokens[sizeof($tokens)-2]=="category"){
			$this->currentCategory=$tokens[sizeof($tokens)-1];
		}
	}

	public function getData() {
		$client = new OCSClient();
		return $client->get($this->OCSCall);
		
	}
    public function CategoryFilter(){
    	$data = $this->getData();
    	echo "
	    <div class=\"container-fluid\">
	      <div class=\"row\">
	        <div class=\"col-sm-3 col-md-2 sidebar\">
	          <ul class=\"nav nav-sidebar sidebutton\">";
	                foreach($data["ocs"]["data"]["category"] as $category){
	                	if ($category["id"]==$this->currentCategory){
	                    	echo "<li class=\"active\"><a href=\"/plasmastore/home/category/".$category["id"]."\">".$category["name"]."</a></li>";
	                    } else {
	                    	echo "<li><a href=\"/plasmastore/home/category/".$category["id"]."\">".$category["name"]."</a></li>";
	                    }
	                }
	            echo "
	          </ul>
	        </div>
	      </div>
	  </div>";
		}




}