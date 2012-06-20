
<?php

include_once("gfx3/lib.php");


echo
"<html>
<body>

<form action=\"v1/content/add\" method=\"post\">
name: <input type=\"text\" name=\"name\"><br>
type: <input type=\"text\" name=\"type\"><br>
downloadname1: <input type=\"text\" name=\"downloadname1\"><br>
downloadlink1: <input type=\"text\" name=\"downloadlink1\"><br>
description: <input type=\"text\" name=\"description\"><br>
summary: <input type=\"text\" name=\"summary\"><br>
version: <input type=\"text\" name=\"version\"><br>
changelog: <input type=\"text\" name=\"changelog\"><br>
<input type=\"submit\">
</form>

</body>
</html>
";

?>
