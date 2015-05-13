<?php

include("gfx3/lib.php");

$id = EHeaderDataParser::db_get("id");

echo "<div class=\"modal-header\">
    <button type=\"button\" class=\"close\" data-dismiss=\"modal\">×</button>
    <h3>Are you sure?</h3>
    </div>
    
    
    <div class=\"modal-body\">
    <h1>Are you sure?</h1>
    <p>Once you delete this game, you won't able to recover it in any way!</p>
    </div>
    
    </div>
    <div class=\"modal-footer\" style=\"background-color:#FFFFFF;\">
    <a href=\"/deleteGameAction.php?id=$id\" target=\"_self\"><div class=\"btn btn-danger\">I'm sure!</div></a>
    </div>
    
    </form>";


?>
