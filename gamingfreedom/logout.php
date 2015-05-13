<?php

include("gfx3/lib.php");

$prevpage = EPageProperties::get_previous_page();

EUser::logout();

header("Location: $prevpage");

?>
