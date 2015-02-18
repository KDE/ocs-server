<?php

include("gfx3/lib.php");

$gameid = EHeaderDataParser::db_get("id");

$client = new OCSClient();
$data = $client->get("v1/content/data/$gameid/");

if(EUser::nick()!=$data["ocs"]["data"]["content"]["personid"]){
	ELog::error("You are not authorized to view this page!");
}

echo "<div class=\"modal-header\">
    <button type=\"button\" class=\"close\" data-dismiss=\"modal\">×</button>
    <h3>Modify your game</h3>
    </div>
    
    <form class=\"form-horizontal\" action=\"/editGameAction.php\" target=\"_self\" method=\"post\">
    <div class=\"modal-body\">
    <fieldset>
    
    <div class=\"control-group\">
    <label class=\"control-label\" for=\"name\">Title</label>
    <div class=\"controls\">
    <input type=\"text\" name=\"name\" class=\"input-xlarge\" id=\"name\" value=\"".EUtility::stripslashes($data["ocs"]["data"]["content"]["name"])."\">
    <input type=\"hidden\" name=\"idcontent\" class=\"input-xlarge\" id=\"idcontent\" value=\"$gameid\">
    </div>
    </div>
    
    <div class=\"control-group\">
    <label class=\"control-label\" for=\"downloadname1\">Download name</label>
    <div class=\"controls\">
    <input type=\"text\" name=\"downloadname1\" class=\"input-xlarge\" id=\"downloadname1\" 
value=\"".EUtility::stripslashes($data["ocs"]["data"]["content"]["downloadname1"])."\">
    <p class=\"help-block\">- this is the label that will be shown to download your game</p>
    </div>
    </div>
    
    <div class=\"control-group\">
    <label class=\"control-label\" for=\"downloadlink1\">Download link</label>
    <div class=\"controls\">
    <input type=\"text\" name=\"downloadlink1\" class=\"input-xlarge\" id=\"downloadlink1\" 
value=\"".EUtility::stripslashes($data["ocs"]["data"]["content"]["downloadlink1"])."\">
    <p class=\"help-block\">- this is the direct link to your game. If you want to host it on gamingfreedom server, you can do it uploading
    it after having created this skeleton</p>
    </div>
    </div>
    
    <div class=\"control-group\">
    <label class=\"control-label\" for=\"description\">Summary</label>
    <div class=\"controls\">
    <textarea cols=\"60\" rows=\"3\" name=\"summary\" class=\"input-xlarge\" 
id=\"summary\">".EUtility::br2nl(EUtility::stripslashes($data["ocs"]["data"]["content"]["summary"]))."</textarea>
    <p class=\"help-block\">- A short description to your game</p>
    </div>
    </div>
    
    <div class=\"control-group\">
    <label class=\"control-label\" for=\"description\">Features</label>
    <div class=\"controls\">
    <textarea cols=\"60\" rows=\"3\" name=\"description\" class=\"input-xlarge\" 
id=\"description\">".EUtility::br2nl(EUtility::stripslashes($data["ocs"]["data"]["content"]["description"]))."</textarea>
    <p class=\"help-block\">- A more in depth description and a list of features.</p>
    </div>
    </div>
    
    <div class=\"control-group\">
    <label class=\"control-label\" for=\"version\">Version</label>
    <div class=\"controls\">
    <input type=\"text\" name=\"version\" class=\"input-xlarge\" id=\"version\" 
value=\"".EUtility::stripslashes($data["ocs"]["data"]["content"]["version"])."\">
    <p class=\"help-block\">- set here the version of your application</p>
    </div>
    </div>
    
    <div class=\"control-group\">
    <label class=\"control-label\" for=\"changelog\">Changelog</label>
    <div class=\"controls\">
    <textarea cols=\"60\" rows=\"3\" name=\"changelog\" class=\"input-xlarge\" 
id=\"changelog\">".EUtility::stripslashes(EUtility::br2nl($data["ocs"]["data"]["content"]["changelog"]))."</textarea>
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
