
<?php

include_once("gfx3/lib.php");

$main = new EMain();


echo
"<html>
<body>

login=\"frank\" password=\"123456\" firstname=\"Frank\" lastname=\"Karlitschek\" email=\"karlitschek@kde.org\"

<form action=\"v1/person/add\" method=\"post\">
login: <input type=\"text\" name=\"login\"><br>
password: <input type=\"text\" name=\"password\"><br>
firstname <input type=\"text\" name=\"firstname\"><br>
lastname <input type=\"text\" name=\"lastname\"><br>
email <input type=\"text\" name=\"email\"><br>
<input type=\"submit\">
</form>

</body>
</html>
";

?>
