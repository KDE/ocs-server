<?php

include("gfx3/lib.php");

//loading template method
EStructure::load();

$client = new OCSClient();
$games = $client->get("v1/content/data?search=&page=1&pagesize=50&sortmode=down&user=");

EStructure::code();
	if(isset($games["ocs"]["data"]["content"])){
		if(isset($games["ocs"]["data"]["content"]["id"])){
			$game = $games["ocs"]["data"]["content"];
				echo "
				<div class=\"span6\">
				<div class=\"row\">
				 <div class=\"span2\"><img src=\"".$game["preview1"]."\"></div>
				 <div class=\"span4\"><h3><a href=\"/game.php/id/".stripslashes($game["id"])."/title/".ERewriter::prettify(stripslashes($game["name"]))."\">".stripslashes($game["name"])."</a></h3>
				 <div class=\"static-rating\" id=\"".$game["score"]."_".$game["id"]."\"></div>
				 <p>".stripslashes($game["summary"])."</p>
				 from <a data-toggle=\"modal\" href=\"#viewSelfProfileModal\" class=\"modalButton\" target=\"/viewProfileModal.php?login=".$game["personid"]."\">".$game["personid"]."</a>
				 </div>
				 </div>
				 <hr>
				 </div>";
		} else {
			foreach($games["ocs"]["data"]["content"] as $game){
				echo "
				<div class=\"span6\">
				<div class=\"row\">
				 <div class=\"span2\"><img src=\"".$game["preview1"]."\"></div>
				 <div class=\"span4\"><h3><a href=\"/game.php/id/".stripslashes($game["id"])."/title/".ERewriter::prettify(stripslashes($game["name"]))."\">".stripslashes($game["name"])."</a></h3>
				 <div class=\"static-rating\" id=\"".$game["score"]."_".$game["id"]."\"></div>
				 <p>".stripslashes($game["summary"])."</p>
				 from <a data-toggle=\"modal\" href=\"#viewSelfProfileModal\" class=\"modalButton\" target=\"/viewProfileModal.php?login=".$game["personid"]."\">".$game["personid"]."</a>
				 </div>
				 </div>
				 <hr>
				 </div>";
			}
		}
	} else {
		echo "Nothing found :(";
	}
EStructure::insert("game_list");

//unloading template method
EStructure::unload();
?>
