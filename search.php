<?php

include("gfx3/lib.php");

$label = EHeaderDataParser::get("label");

header("Location: /gamelist.php/label/$label");

?>
