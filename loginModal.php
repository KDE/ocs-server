<?php
echo "
<div class=\"modal-header\">
    <button type=\"button\" class=\"close\" data-dismiss=\"modal\">×</button>
    <h3>Login Info</h3>
    </div>
    
    <form class=\"form-horizontal\" action=\"/login.php\" 
target=\"_self\" method=\"post\">
    <div class=\"modal-body\">
    <fieldset>
    <div class=\"control-group\">
    <label class=\"control-label\" for=\"login\">Nickname</label>
    <div class=\"controls\">
    <input type=\"text\" name=\"login\" class=\"input-xlarge\" id=\"login\">
    </div>
    </div>
    <div class=\"control-group\">
    <label class=\"control-label\" for=\"password\">Password</label>
    <div class=\"controls\">
    <input type=\"password\" name=\"password\" class=\"input-xlarge\" id=\"password\">
    <p class=\"help-block\">Have you forgot your password?</p>
    </div>
    </div>
    </fieldset>
    
    
    </div>
    <div class=\"modal-footer\" style=\"background-color:#FFFFFF;\">
    <input type=\"submit\" class=\"btn btn-primary\" value=\"Login\">
    </div>
    
    </form>
";
?>
