<?php

include("gfx3/lib.php");

echo "

<xml>
	<highlight>
		<item>
			<title>".EConfig::$data['homecontent'][1]."</title>
			<image>".EConfig::$data['homecontent'][0]."</image>
			<description>".EConfig::$data['homecontent'][2]."</description>
		</item>
		<item> 
                        <title>".EConfig::$data['homecontent'][4]."</title>
                        <image>".EConfig::$data['homecontent'][3]."</image>
                        <description>".EConfig::$data['homecontent'][5]."</description>
                </item>
		<item> 
                        <title>".EConfig::$data['homecontent'][7]."</title>
                        <image>".EConfig::$data['homecontent'][6]."</image>
                        <description>".EConfig::$data['homecontent'][8]."</description>
                </item>
	</highlight>
</xml>

";

?>
