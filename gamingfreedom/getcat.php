<?php

include("gfx3/lib.php");

$client = new OCSClient();

$categories = $client->get("v1/content/categories");

var_dump($categories);

?>
