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
        
        if(!isset($args[0])){
			echo '<h3>Database</h3>';
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
				echo '<a href="/admin/status">Back to database</a>';
				
				EDatabase::q("
				DROP TABLE IF EXISTS `ocs_apitraffic`;
				DROP TABLE IF EXISTS `ocs_comment`;
				DROP TABLE IF EXISTS `ocs_content`;
				DROP TABLE IF EXISTS `ocs_fan`;
				DROP TABLE IF EXISTS `ocs_person`;
				DROP TABLE IF EXISTS `ocs_activity`;
				DROP TABLE IF EXISTS `ocs_friendship`;
				DROP TABLE IF EXISTS `ocs_friendinvitation`;
				");
				
				EDatabase::q("
				CREATE TABLE IF NOT EXISTS `ocs_apitraffic` (
				  `ip` bigint(20) NOT NULL,
				  `count` int(11) NOT NULL,
				  PRIMARY KEY (`ip`)
				) ENGINE=MyISAM;
				");

				EDatabase::q("CREATE TABLE IF NOT EXISTS `ocs_comment` (
				  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				  `type` tinyint(1) NOT NULL,
				  `owner` int(11) NOT NULL,
				  `content` int(11) NOT NULL,
				  `content2` int(11) NOT NULL,
				  `parent` int(11) NOT NULL,
				  `votes` int(11) NOT NULL DEFAULT '0',
				  `score` int(3) NOT NULL DEFAULT '0',
				  `subject` varchar(255) NOT NULL,
				  `date` varchar(50) NOT NULL,
				  `message` text NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB;
				");
				EDatabase::q("CREATE TABLE IF NOT EXISTS `ocs_content` (
				  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				  `owner` int(11) NOT NULL,
				  `votes` int(11) NOT NULL DEFAULT '1',
				  `score` int(3) NOT NULL DEFAULT '50',
				  `downloads` int(11) NOT NULL DEFAULT '0',
				  `license` tinyint(1) NOT NULL DEFAULT '0',
				  `name` varchar(255) NOT NULL,
				  `type` varchar(45) NOT NULL,
				  `downloadname1` varchar(255) DEFAULT NULL,
				  `downloadlink1` varchar(255) DEFAULT NULL,
				  `preview1` varchar(255) NOT NULL DEFAULT 'http://gamingfreedom.org/screenshot-unavailable.png',
				  `preview2` varchar(255) NOT NULL DEFAULT 'http://gamingfreedom.org/screenshot-unavailable.png',
				  `preview3` varchar(255) NOT NULL DEFAULT 'http://gamingfreedom.org/screenshot-unavailable.png',
				  `personid` varchar(255) NOT NULL,
				  `version` varchar(25) DEFAULT NULL,
				  `summary` text,
				  `description` text,
				  `changelog` text,
				  PRIMARY KEY (`id`),
				  KEY `score` (`score`),
				  KEY `personid` (`personid`)
				) ENGINE=MyISAM;
				");
				EDatabase::q("CREATE TABLE IF NOT EXISTS `ocs_fan` (
				  `person` int(11) NOT NULL,
				  `content` int(11) NOT NULL,
				  KEY `person` (`person`,`content`)
				) ENGINE=InnoDB;
				");
				EDatabase::q("CREATE TABLE IF NOT EXISTS `ocs_person` (
				  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				  `login` varchar(45) NOT NULL,
				  `password` varchar(45) NOT NULL,
				  `firstname` varchar(45) NOT NULL,
				  `lastname` varchar(45) NOT NULL,
				  `email` varchar(100) NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM;");

				EDatabase::q("DROP TABLE IF EXISTS `ocs_activity`;
				CREATE TABLE `ocs_activity` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `type` int(2) NOT NULL,
				  `person` int(11) NOT NULL,
				  `timestamp` int(15) NOT NULL,
				  `message` text NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `person` (`person`)
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;");

				EDatabase::q("DROP TABLE IF EXISTS `ocs_friendship`;
				CREATE TABLE `ocs_friendship` (
				  `id1` int(11) NOT NULL,
				  `id2` int(11) NOT NULL,
				  UNIQUE KEY `id1` (`id1`,`id2`)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

				EDatabase::q("DROP TABLE IF EXISTS `ocs_friendinvitation`;
				CREATE TABLE `ocs_friendinvitation` (
				  `fromuser` varchar(255) NOT NULL,
				  `touser` varchar(255) NOT NULL,
				  `message` text NOT NULL,
				  UNIQUE KEY `from` (`fromuser`,`touser`),
				  KEY `fromuser` (`fromuser`),
				  KEY `touser` (`touser`),
				  KEY `fromuser_2` (`fromuser`),
				  KEY `touser_2` (`touser`),
				  KEY `fromuser_3` (`fromuser`)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
				
			} else if($args[0]=='testdata'){
				echo '<p>adding test/password user..........';
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
			}
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
		
		echo '<p>person/data..........';
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("test","password");
		$check = $client->get("v1/person/data");
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
		$contentid = $this->example_contentid = $check['ocs']['data']['content'][0]['id'];
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
		
		echo '<p>comments/data/[type]/[contentid1]/[contentid2]..........';
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		//$client->set_auth_info("test","password");
		$this->_statuscode_test($check, $client);
		$check = $client->get("v1/comments/data/1/$id/1&page=1&pagesize=10");
		
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
         * TODO: add to OCS specs more returncodes than just 100
         */
        echo '<p>friend/data/[personid]..........';
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("test","password");
		$check = $client->get("v1/friend/data/cavolfiore");
		$this->_statuscode_test($check, $client);
        
        /*
         * TODO: add to OCS specs more returncodes than just 100
         */
        echo '<p>friend/receivedinvitations..........';
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("test","password");
		$check = $client->get("v1/friend/receivedinvitations");
		$this->_statuscode_test($check, $client);
		
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
        
        echo '<p>friend/approve/[personid]..........';
        $postdata = array(
			"message" => "would you be my friend?"
			);
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("cavolfiore","cavolfiore");
		$client->set_post_data($postdata);
		$check = $client->post("v1/friend/approve/test");
		$this->_statuscode_test($check, $client);
        
        echo '<p>friend/decline/[personid]..........';
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("cavolfiore","cavolfiore");
		$check = $client->post("v1/friend/decline/test");
		$this->_statuscode_test($check, $client);
		
		echo '<p>friend/cancel/[personid]..........';
		$client = new OCSClient(EConfig::$data["ocs"]["host"]);
		$client->set_auth_info("cavolfiore","cavolfiore");
		$check = $client->post("v1/friend/cancel/test");
		$this->_statuscode_test($check, $client);
        
        /*
         * 
        */
        
        EStructure::view("footer");
	}
    
}

?>
