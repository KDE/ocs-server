<?php

include("gfx3/lib.php");

$idcontent = EHeaderDataParser::db_get("id");
$n = EHeaderDataParser::db_get("n");

echo "<div class=\"modal-header\">
    <button type=\"button\" class=\"close\" data-dismiss=\"modal\">×</button>
    <h3>Upload a screenshot</h3>
    </div>
    
    <form class=\"form-horizontal\" action=\"/addScreenShotGameAction.php\" enctype=\"multipart/form-data\" target=\"_self\" method=\"post\">
    <div class=\"modal-body\">
    <fieldset>
    
    <div class=\"control-group\">
    <label class=\"control-label\" for=\"name\">Image</label>
    <div class=\"controls\">
    <input type=\"file\" name=\"localfile\" class=\"input-xlarge\" id=\"localfile\">
    <input type=\"hidden\" name=\"idcontent\" class=\"input-xlarge\" id=\"idcontent\" value=\"$idcontent\">
    <input type=\"hidden\" name=\"number\" class=\"input-xlarge\" id=\"number\" value=\"$n\">
      <input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"10000000\" /> 
    </div>
    </div>
    
    </fieldset>
    
    </div>
    <div class=\"modal-footer\" style=\"background-color:#FFFFFF;\">
    <input type=\"submit\" class=\"btn btn-primary\" value=\"Next\">
    </div>
    
    </form>";

?>
