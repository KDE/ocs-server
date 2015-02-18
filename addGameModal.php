<?php

include("gfx3/lib.php");

echo "
<div class=\"modal-header\">
    <button type=\"button\" class=\"close\" data-dismiss=\"modal\">×</button>
    <h3>Add a new game</h3>
    </div>
    
    <form class=\"form-horizontal\" action=\"/addgame.php\" target=\"_self\" method=\"post\">
    <div class=\"modal-body\">
    <fieldset>
    
    <div class=\"control-group\">
    <label class=\"control-label\" for=\"name\">Title</label>
    <div class=\"controls\">
    <input type=\"text\" name=\"name\" class=\"input-xlarge\" id=\"name\">
    <input type=\"hidden\" name=\"type\" class=\"input-xlarge\" value=\"1\">
    </div>
    </div>
    
    <div class=\"control-group\">
    <label class=\"control-label\" for=\"downloadname1\">Download name</label>
    <div class=\"controls\">
    <input type=\"text\" name=\"downloadname1\" class=\"input-xlarge\" id=\"downloadname1\">
    <p class=\"help-block\">- this is the label that will be shown to download your game</p>
    </div>
    </div>
    
    <div class=\"control-group\">
    <label class=\"control-label\" for=\"downloadlink1\">Download link</label>
    <div class=\"controls\">
    <input type=\"text\" name=\"downloadlink1\" class=\"input-xlarge\" id=\"downloadlink1\">
    <p class=\"help-block\">- this is the direct link to your game. If you want to host it on gamingfreedom server, you can do it uploading
    it after having created this skeleton</p>
    </div>
    </div>
    
    <div class=\"control-group\">
    <label class=\"control-label\" for=\"summary\">Summary</label>
    <div class=\"controls\">
    <textarea cols=\"60\" rows=\"3\" name=\"summary\" class=\"input-xlarge\" id=\"summary\"></textarea>
    <p class=\"help-block\">- A summary viewable also on previews</p>
    </div>
    </div>
    
    <div class=\"control-group\">
    <label class=\"control-label\" for=\"description\">Description</label>
    <div class=\"controls\">
    <textarea cols=\"60\" rows=\"3\" name=\"description\" class=\"input-xlarge\" id=\"description\"></textarea>
    <p class=\"help-block\">- A short description to your game</p>
    </div>
    </div>
    
    <div class=\"control-group\">
    <label class=\"control-label\" for=\"version\">Version</label>
    <div class=\"controls\">
    <input type=\"text\" name=\"version\" class=\"input-xlarge\" id=\"version\">
    <p class=\"help-block\">- set here the version of your application</p>
    </div>
    </div>
    
    <div class=\"control-group\">
    <label class=\"control-label\" for=\"changelog\">Changelog</label>
    <div class=\"controls\">
    <textarea cols=\"60\" rows=\"3\" name=\"changelog\" class=\"input-xlarge\" id=\"changelog\"></textarea>
    <p class=\"help-block\">- putting changelog helps your fans to know what is changed and what improvements
    have you put</p>
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
