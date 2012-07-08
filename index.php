<?php

include_once("gfx3/lib.php");

?>
<html>
<body>

<form action="http://snizzo:pec@localhost/v1/fan/add/4" method="post">
<input name="contentid" type="hidden" value="4">
<input type="submit">
</form>

<form action="http://snizzo:pec@localhost/v1/fan/status/4" method="post">
<input name="contentid" type="hidden" value="4">
<input type="submit">
</form>

</body>
</html>
