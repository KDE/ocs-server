<?php
/*
 * on this file gfx inclusion is useless as gfx is already running
 */

class StatusController extends EController
{
	
	private $example_contentid;
	
	public function logout()
	{
		EProtect::logout();
	}
	
    public function index($args)
    {	
        EStructure::view("header");
        
        if(!isset($args[0])){
			echo '<h3>OCS Actions</h3>';
			echo '<p>If you put your server in production or experience malfunctionings, you better regenerate providers.xml!<br>
			<a href="/admin/steps/step2">CONFIGURE OCS SERVER</a></p>';
			echo '<p>Change password for accessing admin panel.<br>
			<a href="/admin/steps/step3">CHANGE PASSWORD</a></p>';
			
		}
        
        EStructure::view("footer");
    }
    
    public function database($args)
    {
		EStructure::view("header");
        
        if(!isset($args[0])){
			echo '<h3>Database</h3>';
			echo '<p>If you just copied your ocs-server files to an other server, you would like to reconfigure database.<br>
			<a href="/admin/steps/step1">CONFIGURE DATABASE</a></p>';
			echo '<p>If you have a broken/unconsistent database running, you can attempt resetting your database.<br>
			<a href="/admin/status/database/reset">RESET DATABASE</a></p>';
			echo '<p>If you want to run sanity tests on your database you should install default data.<br>
			This is not reccomended on production and generally useful only for developers.<br>
			<a href="/admin/status/database/testdata">INSTALL TEST DATA</a></p>';
			
		}
        
        if(isset($args[0])){
			echo '<hr>';
			if($args[0]=='reset'){
				echo '<h3>Database</h3>';
				echo 'Resetting...Done!<br>';
				echo 'Are you willing to execute tests? You better <a href="/admin/status/database/testdata">install default data</a>';
				echo '<br><a href="/admin/status/database">Back to database panel</a>';
				
				//using ocstest external library in /admin/libs
				OCSTest::reset_ocs_database();
				
			} else if($args[0]=='testdata'){
				echo '<p>adding test/password user..........';
				$postdata = array(
					"login" => "test",
					"password" => "password",
					"email" => "bababa@bebebe.bu",
					"firstname" => "cavolf",
					"lastname" => "chiappe"
					);
				$client = new OCSClient(EConfig::$data["ocs"]["host"]);
				$check = $client->post("v1/person/add",$postdata);
				$this->_statuscode_test($check, $client);
				echo '<a href="/admin/status/database">Back to database panel</a>';
			}
		}
        
        EStructure::view("footer");
	}
	
	public function categories($args)
    {
		EStructure::view("header");
        
        $categories_path = ELoader::$prev_path.'/config/ocs_categories.conf.php';
        
        $cf = new EConfigFile();
        $cf->set_abs_file($categories_path);
        //$cf->del('4');
        //$cf->save();
        
        if(isset($args[0])){
			$key = EHeaderDataParser::get('key');
			$value = EHeaderDataParser::get('value');
			
			if(!empty($key)){
				if($args[0]=='mod'){
					$cf->set($key,$value);
					$cf->save();
					header("location: /admin/status/categories");
				}
				if($args[0]=='del'){
					$cf->del($key);
					$cf->save();
					header("location: /admin/status/categories");
				}
			}
		}
        
        echo '<h3>Categories</h3>';
        echo '<p>Current OCS categories on server:</p>';
        
        $data = $cf->get_data();
        
        echo "<ul>";
        foreach($data as $key => $value){
			echo "<li>$key | $value</li>";
		}
        echo "</ul>";
        
        echo ' <form action="/admin/status/categories/mod" method="get">
				<input type="text" name="key" placeholder="key"><input type="text" name="value" placeholder="value">
				<input type="submit" value="modify/add category"></form> ';
        
        echo ' <form action="/admin/status/categories/del" method="get">
				<input type="text" name="key" placeholder="key">
				<input type="submit" value="delete category"></form> ';
        
        EStructure::view("footer");
	}
	
	private function _statuscode_test($data, $client)
	{
		ob_start();
		var_dump($data);
		$a=ob_get_contents();
		ob_end_clean();
		if(isset($data["ocs"]["meta"]["statuscode"])){
			echo '<span style="color:green">ok!</span>(statuscode:'.$data["ocs"]["meta"]["statuscode"].')';
			echo '<a onclick="alert(\'';
			echo str_replace("lkjjjnnh", "\\n", addslashes(htmlspecialchars(str_replace("\n", "lkjjjnnh", $a))));
			echo '\')">[show full response]</a></p>';
		} else {
			echo '<span style="color:red">failed!</span></p>';
			ELog::pd($client->get_last_raw_result());
		}
	}
	
	public function test()
	{
		$total_time = 0;
		
		EStructure::view("header");
		
		ETime::measure_from();
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
        
        echo '<h3>Sanity OCS test</h3>';
        
        echo '<p>config..........';
        $check = $client->get("v1/config");
		$this->_statuscode_test($check, $client);
        $time = ETime::measure_to(); $total_time += $time;
        echo "( $time )";
        
        ETime::measure_from();
        echo '<p>person/check..........';
        $postdata = array( "login" => "test", "password" => "password" );
        $check = $client->post("v1/person/check",$postdata);
		$this->_statuscode_test($check, $client);
		$time = ETime::measure_to(); $total_time += $time;
        echo "( $time )";
        
        ETime::measure_from();
        echo '<p>person/add..........';        
		$postdata = array(
			"login" => "cavolfiore",
			"password" => "cavolfiore",
			"email" => "bababa@bebebe.bu",
			"firstname" => "cavolf",
			"lastname" => "chiappe"
			);
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$check = $client->post("v1/person/add",$postdata);
		$this->_statuscode_test($check, $client);
		$time = ETime::measure_to(); $total_time += $time;
        echo "( $time )";
		
		ETime::measure_from();
		echo '<p>person/data..........';
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("test","password");
		$check = $client->get("v1/person/data");
		$this->_statuscode_test($check, $client);
		$time = ETime::measure_to(); $total_time += $time;
        echo "( $time )";
		
		ETime::measure_from();
		echo '<p>person/data?name=cavol&page=1&pagesize=10..........';
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("test","password");
		$check = $client->get("v1/person/data?name=cavol&page=1&pagesize=10");
		$this->_statuscode_test($check, $client);
		$time = ETime::measure_to(); $total_time += $time;
        echo "( $time )";
		
		ETime::measure_from();
		echo '<p>person/data/[login]..........';
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("test","password");
		$check = $client->get("v1/person/data/cavolfiore");
		$this->_statuscode_test($check, $client);
		$time = ETime::measure_to(); $total_time += $time;
        echo "( $time )";
		
		ETime::measure_from();
		echo '<p>person/self..........';
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("test","password");
		$check = $client->get("v1/person/self");
		$this->_statuscode_test($check, $client);
		$time = ETime::measure_to(); $total_time += $time;
        echo "( $time )";
		
		ETime::measure_from();
		echo '<p>content/add..........';
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$postdata = array(
			"name" => "esempio",
			"type" => "0",
			"downloadname1" => "downloadname1",
			"downloadlink1" => "downloadlink1",
			"description" => "description",
			"summary" => "summary",
			"version" => "version",
			"changelog" => "changelog",
			"personid" => "test"
			);
		$client->set_auth_info("test","password");
		$check = $client->post("v1/content/add",$postdata);
		$contentid = $this->example_contentid = $check['ocs']['data']['content'][0]['id'];
        $this->_statuscode_test($check, $client);
		$time = ETime::measure_to(); $total_time += $time;
        echo "( $time )";
        
        ETime::measure_from();
        echo '<p>content/categories..........';
        $check = $client->get("v1/content/categories");
		$this->_statuscode_test($check, $client);
		$time = ETime::measure_to(); $total_time += $time;
        echo "( $time )";
		
		ETime::measure_from();
		echo '<p>content/licenses..........';
        $check = $client->get("v1/content/licenses");
		$this->_statuscode_test($check, $client);
		$time = ETime::measure_to(); $total_time += $time;
        echo "( $time )";
		
		ETime::measure_from();
		echo '<p>content/data..........';
        $check = $client->get("v1/content/data");
		$this->_statuscode_test($check, $client);
		$time = ETime::measure_to(); $total_time += $time;
        echo "( $time )";
        
        ETime::measure_from();
        echo '<p>content/data?search=esem..........';
        $check = $client->get("v1/content/data?search=esem");
		$this->_statuscode_test($check, $client);
		$time = ETime::measure_to(); $total_time += $time;
        echo "( $time )";
        
        ETime::measure_from();
        echo '<p>content/data/[contentid]..........';
        $check = $client->get("v1/content/data/$contentid");
		$this->_statuscode_test($check, $client);
		$time = ETime::measure_to(); $total_time += $time;
        echo "( $time )";
        
        ETime::measure_from();
        echo '<p>content/download/[contentid]/1..........';
        $check = $client->get("v1/content/download/$contentid/1");
		$this->_statuscode_test($check, $client);
		$time = ETime::measure_to(); $total_time += $time;
        echo "( $time )";
		
		ETime::measure_from();
		echo '<p>content/vote/[contentid]..........';
		$id = intval($contentid);
		$rate = floatval(1);
		
		$postdata = array(
			"vote" => $rate
			);
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("test","password");
		$client->set_post_data($postdata);
		$check = $client->post("v1/content/vote/$id");
		$this->_statuscode_test($check, $client);
		$time = ETime::measure_to(); $total_time += $time;
        echo "( $time )";
        
        ETime::measure_from();
        echo '<p>content/edit/[contentid]..........';
		$postdata = array(
			"name" => "esempiomod",
			"summary" => "summarymod",
			"downloadname1" => "downloadname1mod",
			"downloadlink1" => "downloadlink1mod",
			"description" => "descriptionmod",
			"version" => "versionmod",
			"changelog" => "changelogmod"
			);

		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("test","password");
		$client->set_post_data($postdata);
		$check = $client->post("v1/content/edit/$id");
		$this->_statuscode_test($check, $client);
		$time = ETime::measure_to(); $total_time += $time;
        echo "( $time )";
		
		ETime::measure_from();
		echo '<p>[get] activity..........';
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("test","password");
		$check = $client->get("v1/activity");
		$this->_statuscode_test($check, $client);
		$time = ETime::measure_to(); $total_time += $time;
        echo "( $time )";
        
        ETime::measure_from();
        echo '<p>[post] activity..........';
        $postdata = array(
			"message" => "coding is fun"
			);
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("test","password");
		$client->set_post_data($postdata);
		$check = $client->post("v1/activity");
		$this->_statuscode_test($check, $client);
		$time = ETime::measure_to(); $total_time += $time;
        echo "( $time )";
        
        ETime::measure_from();
        echo '<p>fan/add..........';
        $postdata = array("idcontent"=>$contentid);
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("test","password");
		$client->set_post_data($postdata);
		$check = $client->post("v1/fan/add/$id");
		$this->_statuscode_test($check, $client);
		$time = ETime::measure_to(); $total_time += $time;
        echo "( $time )";
		
		ETime::measure_from();
		echo '<p>fan/data/[contentid]..........';
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("test","password");
		$check = $client->get("v1/fan/data/$id");
		$this->_statuscode_test($check, $client);
		$time = ETime::measure_to(); $total_time += $time;
        echo "( $time )";
		
		echo '<p>fan/status/[contentid]..........';
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("test","password");
		$check = $client->get("v1/fan/status/$id");
		$this->_statuscode_test($check, $client);
		$time = ETime::measure_to(); $total_time += $time;
        echo "( $time )";
		
		echo '<p>fan/remove..........';
        $postdata = array("idcontent"=>$contentid);
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("test","password");
		$client->set_post_data($postdata);
		$check = $client->post("v1/fan/remove/$id");
		$this->_statuscode_test($check, $client);
		$time = ETime::measure_to(); $total_time += $time;
        echo "( $time )";
		
		echo '<p>comments/add..........';
		$postdata = array(
			"type" => "1",
			"content" => $id,
			"content2" => "1",
			"parent" => "0",
			"subject" => "subject",
			"message" => "message"
			);
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("test","password");
		$client->set_post_data($postdata);
		$check = $client->post("v1/comments/add");
		$this->_statuscode_test($check, $client);
		$time = ETime::measure_to(); $total_time += $time;
        echo "( $time )";
		
		echo '<p>comments/data/[type]/[contentid1]/[contentid2]..........';
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		//$client->set_auth_info("test","password");
		$check = $client->get("v1/comments/data/1/$id/1&page=1&pagesize=10");
		$this->_statuscode_test($check, $client);
		$time = ETime::measure_to(); $total_time += $time;
        echo "( $time )";
		
		//deleting content used for tests
		
		echo '<p>content/delete/[contentid]..........';
		$postdata = array(
			"contentid" => $id
			);
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("test","password");
		$client->set_post_data($postdata);
		$check = $client->post("v1/content/delete/$id");
		$this->_statuscode_test($check, $client);
		$time = ETime::measure_to(); $total_time += $time;
        echo "( $time )";
        
        /*
         * TODO: add to OCS specs more returncodes than just 100
         */
        echo '<p>friend/data/[personid]..........';
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("test","password");
		$check = $client->get("v1/friend/data/cavolfiore");
		$this->_statuscode_test($check, $client);
		$time = ETime::measure_to(); $total_time += $time;
        echo "( $time )";
        
        /*
         * TODO: add to OCS specs more returncodes than just 100
         */
        echo '<p>friend/receivedinvitations..........';
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("test","password");
		$check = $client->get("v1/friend/receivedinvitations");
		$this->_statuscode_test($check, $client);
		$time = ETime::measure_to(); $total_time += $time;
        echo "( $time )";
		
        /*
         * TODO: add to OCS specs more returncodes than just 100
         */
        echo '<p>friend/invite/[personid]..........';
        $postdata = array(
			"message" => "would you be my friend?"
			);
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("test","password");
		$client->set_post_data($postdata);
		$check = $client->post("v1/friend/invite/cavolfiore ");
		$this->_statuscode_test($check, $client);
		$time = ETime::measure_to(); $total_time += $time;
        echo "( $time )";
        
        echo '<p>friend/approve/[personid]..........';
        $postdata = array(
			"message" => "would you be my friend?"
			);
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("cavolfiore","cavolfiore");
		$client->set_post_data($postdata);
		$check = $client->post("v1/friend/approve/test");
		$this->_statuscode_test($check, $client);
		$time = ETime::measure_to(); $total_time += $time;
        echo "( $time )";
        
        echo '<p>friend/decline/[personid]..........';
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("cavolfiore","cavolfiore");
		$check = $client->post("v1/friend/decline/test");
		$this->_statuscode_test($check, $client);
		$time = ETime::measure_to(); $total_time += $time;
        echo "( $time )";
		
		echo '<p>friend/cancel/[personid]..........';
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("cavolfiore","cavolfiore");
		$check = $client->post("v1/friend/cancel/test");
		$this->_statuscode_test($check, $client);
		$time = ETime::measure_to(); $total_time += $time;
        echo "( $time )<br>";
        
        echo "This OCS test took: $total_time";
        
        EStructure::view("footer");
	}
    
}

?>
