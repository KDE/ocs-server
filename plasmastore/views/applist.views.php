<?php
if(isset($data[0]["ocs"]["data"])){
	foreach($data[0]["ocs"]["data"]["content"] as $content){
		echo "
		<div class=\"row\">
			<div class=\"col-md-7\">
				<a href=\"#\">
					<img class=\"img-responsive\" src=\"".$content["preview1"]."\" alt=\"\">
				</a>
			</div>
			<div class=\"col-md-5\">
				<h3>".$content["name"]."</h3>";
				if(OCSUser::is_logged()) {
					echo "<a href=/plasmastore/publicprofile/".OCSUser::login()."><h4>".$content["personid"]."</h4></a>";
				}
				else{
					echo "<h4>".$content["personid"]."</h4>";
				}

				echo "
				<p>".$content["summary"]."</p>
				<a class=\"btn btn-primary\" href=\"/plasmastore/app_description/show/".$content["id"]."/".ERewriter::prettify($content["name"])."\">View Project <span class=\"glyphicon glyphicon-chevron-right\"></span></a>
				";
				if(OCSUser::is_logged() && $_COOKIE["login"]==$content["personid"]){
					echo "<a class=\"btn btn-danger\" href=\"/plasmastore/home/delData/".$content["id"]."\">Delete <span class=\"glyphicon glyphicon-trash\"></span></a>";
				}
				echo "
			</div>
		</div> <hr>";

	}
} ?>
