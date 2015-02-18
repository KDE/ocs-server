<?php

include("gfx3/lib.php");

if(EHeaderDataParser::exists_get("id")){
	$id = EHeaderDataParser::db_get("id");

	$users = new EData("ocs_person");
	$user_info = $users->find("firstname,lastname,email,login","where id=$id limit 1");
	$user_info = $user_info[0]; //taking first element
} elseif(EHeaderDataParser::exists_get("login")) {
	$login = EHeaderDataParser::db_get("login");

	$users = new EData("ocs_person");
	$user_info = $users->find("firstname,lastname,email,login","where login='$login' limit 1");
	$user_info = $user_info[0]; //taking first element
}
echo "
<div class=\"modal-header\">
    <button type=\"button\" class=\"close\" data-dismiss=\"modal\">×</button>
    <h3>Personal info of ".$user_info["firstname"]." ".$user_info["lastname"]."</h3>
</div>

    <div class=\"modal-body\">
    <table class=\"mytable\">
		<tr><td>First Name:</td><td><span class=\"right\">".$user_info["firstname"]."</span></td></tr>
		<tr><td>Last Name:</td><td><span class=\"right\">".$user_info["lastname"]."</span></td></tr>
		<tr><td>User Name:</td><td><span class=\"right\">".$user_info["login"]."</span></td></tr>
		<tr><td>E-mail:</td><td><span class=\"right\">".$user_info["email"]."</span></td></tr>
    </table>
    <br>
    <a href=\"#\">Find all games from this user</a><br>
	<a href=\"#\">Find all games liked by this user</a>
    </div>
    
    <div class=\"modal-footer\" style=\"background-color:#F5F5F5;\">
    <a href=\"#\" class=\"btn\" data-dismiss=\"modal\">Close</a>
    </div>

    ";

?>
