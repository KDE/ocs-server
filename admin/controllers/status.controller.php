<?php
/*
 * on this file gfx inclusion is useless as gfx is already running
 */

class StatusController extends EController
{
	
	private $example_contentid;
	
    public function index($args)
    {	
        EStructure::view("header");
        
        echo "<h3>Server</h3>";
        
        EStructure::view("footer");
    }
    
    public function database($args)
    {
		EStructure::view("header");
        
        if(isset($args[0])){
			if($args[0]=='reset'){
				echo '<h3>Database</h3>';
				echo 'Resetting...Done!';
				//code that resets database
			}
		} else {
			echo '<h3>Database</h3>';
			echo '<p>If you have a broken/unconsistent database running, you can attempt resetting your database.<br>
			<a href="/admin/status/database/reset">RESET DATABASE</a></p>';
		}
        
        EStructure::view("footer");
	}
	
	public function categories()
    {
		EStructure::view("header");
        
        echo '<h3>Categories</h3>';
        echo '<p>Here you can manage OCS categories:</p>';
        //code that prints database category table
        
        EStructure::view("footer");
	}
	
	private function _statuscode_test($data, $client)
	{
		if(isset($data["ocs"]["meta"]["statuscode"])){
			echo '<span style="color:green">ok!</span>(statuscode:'.$data["ocs"]["meta"]["statuscode"].')</p>';
		} else {
			echo '<span style="color:red">failed!</span></p>';
			ELog::pd($client->get_last_raw_result());
		}
	}
	
	public function test()
	{
		EStructure::view("header");
		
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
        
        echo '<h3>Sanity OCS test</h3>';
        
        echo '<p>config..........';
        $check = $client->get("v1/config");
		$this->_statuscode_test($check, $client);
        
        echo '<p>person/check..........';
        $postdata = array( "login" => "test", "password" => "password" );
        $check = $client->post("v1/person/check",$postdata);
		$this->_statuscode_test($check, $client);
        
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
		
		echo '<p>person/data?name=cavol&page=1&pagesize=10..........';
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("test","password");
		$check = $client->get("v1/person/data?name=cavol&page=1&pagesize=10");
		$this->_statuscode_test($check, $client);
		
		echo '<p>person/data/[login]..........';
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("test","password");
		$check = $client->get("v1/person/data/cavolfiore");
		$this->_statuscode_test($check, $client);
		
		echo '<p>person/self..........';
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("test","password");
		$check = $client->get("v1/person/self");
		$this->_statuscode_test($check, $client);
		
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
		$contentid = $this->example_contentid = $check['ocs']['data']['content']['id'];
        $this->_statuscode_test($check, $client);
        
        echo '<p>content/categories..........';
        $check = $client->get("v1/content/categories");
		$this->_statuscode_test($check, $client);
		
		echo '<p>content/licenses..........';
        $check = $client->get("v1/content/licenses");
		$this->_statuscode_test($check, $client);
		
		echo '<p>content/data..........';
        $check = $client->get("v1/content/data");
		$this->_statuscode_test($check, $client);
        
        echo '<p>content/data?search=esem..........';
        $check = $client->get("v1/content/data?search=esem");
		$this->_statuscode_test($check, $client);
        
        echo '<p>content/data/[contentid]..........';
        $check = $client->get("v1/content/data/$contentid");
		$this->_statuscode_test($check, $client);
        
        echo '<p>content/download/[contentid]/1..........';
        $check = $client->get("v1/content/download/$contentid/1");
		$this->_statuscode_test($check, $client);
		
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
		
		echo '<p>[get] activity..........';
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("test","password");
		$check = $client->get("v1/activity");
		$this->_statuscode_test($check, $client);
        
        echo '<p>[post] activity..........';
        $postdata = array(
			"message" => "coding is fun"
			);
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("test","password");
		$client->set_post_data($postdata);
		$check = $client->post("v1/activity");
		$this->_statuscode_test($check, $client);
        
        echo '<p>fan/add..........';
        $postdata = array("idcontent"=>$contentid);
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("test","password");
		$client->set_post_data($postdata);
		$check = $client->post("v1/fan/add/$id");
		$this->_statuscode_test($check, $client);
		
		echo '<p>fan/data/[contentid]..........';
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("test","password");
		$check = $client->get("v1/fan/data/$id");
		$this->_statuscode_test($check, $client);
		
		echo '<p>fan/status/[contentid]..........';
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("test","password");
		$check = $client->get("v1/fan/status/$id");
		$this->_statuscode_test($check, $client);
		
		echo '<p>fan/remove..........';
        $postdata = array("idcontent"=>$contentid);
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("test","password");
		$client->set_post_data($postdata);
		$check = $client->post("v1/fan/remove/$id");
		$this->_statuscode_test($check, $client);
		
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
        
        /*
         * 
        */
        
        EStructure::view("footer");
	}
    
}

?>
