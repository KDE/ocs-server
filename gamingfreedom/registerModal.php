<?php
echo "
<div class=\"modal-header\">
    <button type=\"button\" class=\"close\" data-dismiss=\"modal\">×</button>
    <h3>Register a new user</h3>
    </div>
    
    <form class=\"form-horizontal\" action=\"/register.php\" target=\"_self\" method=\"post\">
    <div class=\"modal-body\">
    <fieldset>
    
    <div class=\"control-group\">
    <label class=\"control-label\" for=\"login\">Nickname</label>
    <div class=\"controls\">
    <input type=\"text\" name=\"login\" class=\"input-xlarge\" id=\"login\">
    <p class=\"help-block\">* must contain just charachters and numbers</p>
    </div>
    </div>
    
    <div class=\"control-group\">
    <label class=\"control-label\" for=\"password\">Password</label>
    <div class=\"controls\">
    <input type=\"password\" name=\"password\" class=\"input-xlarge\" id=\"password\">
    <p class=\"help-block\">* must be a minimum of 8 characters</p>
    </div>
    </div>
    
    <div class=\"control-group\">
    <label class=\"control-label\" for=\"email\">E-Mail</label>
    <div class=\"controls\">
    <input type=\"text\" name=\"email\" class=\"input-xlarge\" id=\"email\">
    <p class=\"help-block\">* must be a valid email</p>
    </div>
    </div>
    
    <div class=\"control-group\">
    <label class=\"control-label\" for=\"firstname\">Firstname</label>
    <div class=\"controls\">
    <input type=\"text\" name=\"firstname\" class=\"input-xlarge\" id=\"firstname\">
    </div>
    </div>
    
    <div class=\"control-group\">
    <label class=\"control-label\" for=\"lastname\">Lastname</label>
    <div class=\"controls\">
    <input type=\"text\" name=\"lastname\" class=\"input-xlarge\" id=\"lastname\">
    </div>
    </div>
    
    </fieldset>
    
    
    </div>
    <div class=\"modal-footer\" style=\"background-color:#FFFFFF;\">
    <input type=\"submit\" class=\"btn btn-primary\" value=\"Next\">
    </div>
    
    </form>
    ";
?>
