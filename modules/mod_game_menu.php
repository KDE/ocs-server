<?php

$gameid = EHeaderDataParser::db_get("id");

echo "<div class=\"span4 columns\">
	<div class=\"well myshadow\">
    <ul class=\"nav nav-list\">
    <li class=\"nav-header\" style=\"color:#8B0000;\">
    Modify this game
    <li>
    <a data-toggle=\"modal\" href=\"#genericModal\" class=\"modalButton\" target=\"/editGameModal.php?id=$gameid\">Modify Skeleton</a>
    <a data-toggle=\"modal\" href=\"#genericModal\" class=\"modalButton\" target=\"/deleteGameModal.php?id=$gameid\">Delete game</a>
    </li>
    <li class=\"divider\"></li>
    <li>
    <a data-toggle=\"modal\" href=\"#genericModal\" class=\"modalButton\" target=\"/addScreenShotGameModal.php?id=$gameid&n=1\">Add first screenshot</a>
    </li>
    <li>
    <a data-toggle=\"modal\" href=\"#genericModal\" class=\"modalButton\" target=\"/addScreenShotGameModal.php?id=$gameid&n=2\">Add second screenshot</a>
    </li>
    <li>
    <a data-toggle=\"modal\" href=\"#genericModal\" class=\"modalButton\" target=\"/addScreenShotGameModal.php?id=$gameid&n=3\">Add third screenshot</a>
    </li>
    <li class=\"divider\"></li>
    <li>
    <a data-toggle=\"modal\" href=\"#genericModal\" class=\"modalButton\" target=\"/uploadGameModal.php?id=$gameid\">Upload game file</a>
    </li>
    </ul>
    </div>
    </div>";

?>
