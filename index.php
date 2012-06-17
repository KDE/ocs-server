
<?php

include_once("gfx3/lib.php");

$main = new EMain();

if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Text to send if user hits Cancel button';
    exit;
} else {
    echo "<p>Hello {$_SERVER['PHP_AUTH_USER']}.</p>";
    echo "<p>You entered {$_SERVER['PHP_AUTH_PW']} as your password.</p>";
}

/*
echo
"<html>
<body>

<form action=\"v1/person/check\" method=\"post\">
login: <input type=\"text\" name=\"login\"><br>
password: <input type=\"text\" name=\"password\"><br>
<input type=\"submit\">
</form>

</body>
</html>
";
*/
?>
