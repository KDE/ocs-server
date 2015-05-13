<?php

include("gfx3/lib.php");

$id = EHeaderDataParser::db_get("id");

$users = new EData("users");
$user_info = $users->find("firstname,lastname,email,password","where id=".EUser::id()."");
$user_info = $user_info[0]; //taking first element

echo "
<div class=\"modal-header\">
    <button type=\"button\" class=\"close\" data-dismiss=\"modal\">×</button>
    <h3>Edit personal info of ".$user_info["firstname"]." ".$user_info["lastname"]."</h3>
</div>

    <form class=\"form-horizontal\" action=\"updateprofileinfo.php\" target=\"_self\" method=\"post\">
    <div class=\"modal-body\">
    <fieldset>
    
    <div class=\"control-group\">
    <label class=\"control-label\" for=\"password\">Password</label>
    <div class=\"controls\">
    <input type=\"password\" name=\"password\" class=\"input-xlarge\" id=\"password\" value=\"".$user_info["password"]."\">
    <input type=\"hidden\" name=\"id\" value=\"".EUser::id()."\">
    <p class=\"help-block\">* must be a minimum of 8 characters</p>
    </div>
    </div>
    
    <div class=\"control-group\">
    <label class=\"control-label\" for=\"email\">E-Mail</label>
    <div class=\"controls\">
    <input type=\"text\" name=\"email\" class=\"input-xlarge\" id=\"email\" value=".$user_info["email"].">
    <p class=\"help-block\">* must be a valid email</p>
    </div>
    </div>
    
    <div class=\"control-group\">
    <label class=\"control-label\" for=\"firstname\">Firstname</label>
    <div class=\"controls\">
    <input type=\"text\" name=\"firstname\" class=\"input-xlarge\" id=\"firstname\" value=".$user_info["firstname"].">
    </div>
    </div>
    
    <div class=\"control-group\">
    <label class=\"control-label\" for=\"lastname\">Lastname</label>
    <div class=\"controls\">
    <input type=\"text\" name=\"lastname\" class=\"input-xlarge\" id=\"lastname\" value=".$user_info["lastname"].">
    </div>
    </div>
    
    </fieldset>
    
    
    </div>
    <div class=\"modal-footer\" style=\"background-color:#FFFFFF;\">
    <input type=\"submit\" class=\"btn btn-primary\" value=\"Save\">
    </div>
    
    </form>

    ";


?>
