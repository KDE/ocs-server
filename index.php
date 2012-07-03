lololol
<?php

include_once("gfx3/lib.php");

?>
<html>
<body>

<form enctype="multipart/form-data" action="http://snizzo:poc@localhost/v1/content/uploaddownload/4" method="post">
<input name="localfile" type="file">
<input type="submit">
</form>

<form enctype="multipart/form-data" action="index.php" method="post">
<input name="localfile" type="file">
<input type="submit">
</form>

</body>
</html>

<?php

var_dump($_GET);
var_dump($_POST);
var_dump($_FILES);

?>
