<?php

include("gfx3/lib.php");

EStructure::load("gamelist");

$from = EHeaderDataParser::db_get("from");
$label = EHeaderDataParser::db_get("label");
$page = EHeaderDataParser::db_get("page");
$user = EHeaderDataParser::db_get("user");

if(!$from){
	$from = "new";
}
if(!$label){
	$label = "";
}
if(!$page){
	$page = 1;
}
if(!$user){
	$user = "";
}

$client = new OCSClient();
$games = $client->get("v1/content/data?search=$label&page=$page&pagesize=50&sortmode=$from&user=$user");

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

EStructure::unload();

?>
