<?php

include("gfx3/lib.php");

$aResponse['error'] = false;
$aResponse['message'] = '';
	
	
if(isset($_POST['action']))
{
	if(htmlentities($_POST['action'], ENT_QUOTES, 'UTF-8') == 'rating')
	{
		$id = intval($_POST['idBox']);
		$rate = floatval($_POST['rate']);
		
		$postdata = array(
			"vote" => $rate
			);
		
		$client = new OCSClient();
		$client->set_auth_info(EUser::nick(),EUser::password());
		$client->set_post_data($postdata);
		$result = $client->post("v1/content/vote/$id");
		
		if($result["ocs"]["status"]=="ok"){
			$success = true;
		} else {
			$success = false;
		}
		
		
		// json datas send to the js file
		if($success)
		{
			$aResponse['message'] = 'Your rate has been successfuly recorded. Thanks for your rate :)';
			
			echo json_encode($aResponse);
		}
		else
		{
			$aResponse['error'] = false;
			$aResponse['message'] = $client->get_last_raw_result();
			
			
			echo json_encode($aResponse);
		}
	}
	else
	{
		$aResponse['error'] = true;
		$aResponse['message'] = '"action" post data not equal to \'rating\'';
			
		
		echo json_encode($aResponse);
	}
}
else
{
	$aResponse['error'] = true;
	$aResponse['message'] = '$_POST[\'action\'] not found';
	
	
	echo json_encode($aResponse);
}
