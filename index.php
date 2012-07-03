<?php

include_once("gfx3/lib.php");

?>
<html>
<body>

<form enctype="multipart/form-data" action="http://snizzo:pec@localhost/v1/content/uploadpreview/4/1" method="post">
<input name="contentid" type="hidden" value="4">
<input name="previewid" type="hidden" value="1">
<input name="localfile" type="file">
<input type="submit">
</form>

</body>
</html>
