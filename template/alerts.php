<?php

if(EHeaderDataParser::out_get("e")){
	$error = EHeaderDataParser::out_get("e");
	
	echo "<script language=\"javascript\">
	alert('".$error."');
	</script>";
}

?>
