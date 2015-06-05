<?php
/*
 * on this file gfx inclusion is useless as gfx is already running
 */

class StatusController extends EController
{
	
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
        
        EStructure::view("footer");
	}
    
}

?>
