<?php

include("gfx3/lib.php");

$gameid = EHeaderDataParser::db_get("id");

EStructure::load("game");

$client = new OCSClient();
$data = $client->get("v1/content/data/$gameid/");

if(EUser::nick()==$data["ocs"]["data"]["content"]["personid"]){
	EStructure::module("mod_game_menu","mod_game");
}

EStructure::code();
	echo stripslashes($data["ocs"]["data"]["content"]["name"]);
Estructure::insert("game_title");

EStructure::code();
	echo stripslashes($data["ocs"]["data"]["content"]["summary"]);
Estructure::insert("game_summary");

//features
EStructure::code();
	echo stripslashes($data["ocs"]["data"]["content"]["description"]);
Estructure::insert("game_description");

EStructure::code();
	echo stripslashes($data["ocs"]["data"]["content"]["changelog"]);
Estructure::insert("game_changelog");

EStructure::code();
	$person = $data["ocs"]["data"]["content"]["personid"];
echo "<a data-toggle=\"modal\" href=\"#viewSelfProfileModal\" class=\"modalButton bold\" target=\"/viewProfileModal.php?login=$person\">$person</a>";
Estructure::insert("game_author");

EStructure::code();
	echo stripslashes($data["ocs"]["data"]["content"]["version"]);
Estructure::insert("game_version");

EStructure::code();
	echo "<div class=\"active item\"><img src=\"".$data["ocs"]["data"]["content"]["preview1"]."\" class=\"myradius\">
					</div>
					<div class=\"item\"><img src=\"".$data["ocs"]["data"]["content"]["preview2"]."\" class=\"myradius\">
					</div>
					<div class=\"item\"><img src=\"".$data["ocs"]["data"]["content"]["preview3"]."\" class=\"myradius\">
					</div>";
EStructure::insert("carousel_images");

EStructure::code();
	if(!empty($data["ocs"]["data"]["content"]["downloadlink1"])){
		echo "<a class=\"btn btn-primary\" href=\"".$data["ocs"]["data"]["content"]["downloadlink1"]."\"><i class=\"icon-download icon-white\"></i> ".$data["ocs"]["data"]["content"]["downloadname1"]."</a>";
	}
EStructure::insert("download_button");

EStructure::code();
	if(EUser::logged()){
		$client->set_auth_info(EUser::nick(),EUser::password());
		$isfan = $client->get("v1/fan/status/$gameid");
		if($isfan["ocs"]["data"]["status"]=="notfan"){
			echo "<a class=\"btn btn-success\" href=\"/becomeFan.php?id=$gameid\"><i class=\"icon-thumbs-up icon-white\"></i> Like this!</a>";
		} else {
			echo "<a class=\"btn btn-success\" href=\"/removeFan.php?id=$gameid\"><i class=\"icon-thumbs-down icon-white\"></i> Don't like 
this!</a>";
		}
	} else {
		echo "<a class=\"btn btn-success\" href=\"#\"><i class=\"icon-thumbs-up icon-white\"></i> Log in to vote!</a>";
	}
EStructure::insert("becomefan_button");

EStructure::code();
	$score = $data["ocs"]["data"]["content"]["score"];
	echo "<div class=\"dynamic-rating\" id=\"".$score."_".$gameid."\"></div>";
EStructure::insert("setscore_button");

EStructure::code();
	$score = $data["ocs"]["data"]["content"]["score"];
	echo "<div class=\"static-rating\" id=\"".$score."_".$gameid."\"></div>";
EStructure::insert("averagescore");

EStructure::code();
	$comments = $client->get("v1/comments/data/1/$gameid/1&page=1&pagesize=10");
	
	if(isset($comments["ocs"]["data"]["comment"])){
		foreach($comments["ocs"]["data"]["comment"] as $comment){
			echo "<div class=\"span6 columns\">
				<h4>".EUtility::stripslashes($comment["subject"])."</h4>
				<div class=\"span6\">".EUtility::stripslashes($comment["text"])."</div>
				written by <a data-toggle=\"modal\" href=\"#viewSelfProfileModal\" class=\"modalButton bold\" target=\"/viewProfileModal.php?login=".$comment["user"]."\">".$comment["user"]."</a>
				<hr>
				</div>";
		}
	} else {
		echo "No comments found :(.";
	}
	echo "<div class=\"span6 columns\">
	<form class=\"form-vertical\" action=\"/addComment.php\" target=\"_self\" method=\"post\">
	<table style=\"width:100%\">
    <tr><td><input type=\"hidden\" name=\"type\" value=\"1\">
    <input type=\"hidden\" name=\"content\" value=\"$gameid\">
    <input type=\"hidden\" name=\"content2\" value=\"1\">
    <input type=\"hidden\" name=\"parent\" value=\"0\">
    <input type=\"text\" name=\"subject\" placeholder=\"Subject\" style=\"width:100%\"></td></tr>
    <br>
    <tr><td><textarea name=\"message\" class=\"input-xlarge\" style=\"width:100%\"></textarea><br></td></tr>
    <tr><td><input type=\"submit\" class=\"btn btn-warning\" value=\"Write\"></td></tr>
    </table>
    </form>
	</div>";
EStructure::insert("game_comments");

EStructure::unload();

?>
