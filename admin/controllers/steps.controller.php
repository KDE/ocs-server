<?php
/*
 * on this file gfx inclusion is useless as gfx is already running
 */

class StepsController extends EController
{
	public function index()
	{
		//empty for now
	}
	
	public function _error($s)
	{
		return '<span style="color:red">'.$s.'</span>';
	}
	
	public function _notify($s)
	{
		return '<span style="color:green">'.$s.'</span>';
	}
	
	public function step1($args)
	{
        $working = false;
        
        $name = EHeaderDataParser::post('name');
        $host = EHeaderDataParser::post('host');
        $user = EHeaderDataParser::post('user');
        $pass = EHeaderDataParser::post('password');
        $pass2 = EHeaderDataParser::post('password2');
        $notification = '';
        
        $database_path = ELoader::$prev_path.'/config/database.conf.php';
		
		$cf = new EConfigFile();
		$cf->set_abs_file($database_path);
        
        if((empty($name)) and !empty($cf->get('name'))){
			$name = $cf->get('name');
		}
		
		if((empty($host)) and !empty($cf->get('host'))){
			$host = $cf->get('host');
		}
		
		if((empty($user)) and !empty($cf->get('user'))){
			$user = $cf->get('user');
		}
		
		if((empty($pass)) and !empty($cf->get('password'))){
			$pass = $pass2 = $cf->get('password');
		}
        
        if(!empty($name) and !empty($user) and !empty($host) and !empty($pass) and !empty($pass2)){
			if($pass!=$pass2){
				$this->_error('Warning! Your passwords didn\'t match! Please reinsert them!');
			} else {
				$cf->set('name', $name);
				$cf->set('user', $user);
				$cf->set('host', $host);
				$cf->set('password', $pass);
				
				EDatabase::set_db_info($name,$host,$user,$pass);
				EUtility::hide_output(); // hiding output as mysqli functions are surely outputting something
				if(!EDatabase::open_session()){
					EUtility::show_output();
					$notification = $this->_error('Couldn\'t open connection to database! Please check config!');
				} else {
					OCSTest::install_ocs_database(); //execute soft install
					$out = EUtility::show_output();
					
					if(!empty($out)){
						$notification = $this->_error('Something went wrong with install phase! Please check config!');
					} else {						
						$notification = $this->_notify('We can connect to database! Database is installed and configuration saved!');
						$working = true;
						$cf->save();
					}
				}
			}
		}
		
		$data = array();
		$data['name'] = $name;
		$data['user'] = $user;
		$data['host'] = $host;
		$data['pass'] = $pass;
		$data['pass2'] = $pass2;
		$data['working'] = $working;
		$data['notification'] = $notification;
		EStructure::view('wizard/step1', $data);
		
	}
	
	public function step2()
	{
		EStructure::view("header");
		if($this->arg_key('save')){
			$ocsserver_path = ELoader::$prev_path.'/config/ocsserver.conf.php';
				
			$cf = new EConfigFile();
			$cf->set_abs_file($ocsserver_path);
			
			$name = $cf->get('name');
			$host = $cf->get('host');
			$website = $cf->get('website');
			$contact = $cf->get('contact');
			$location = $cf->get('location');
			$ssl = $cf->get('ssl');
			$format = $cf->get('format');
			$termsofuse = $cf->get('termsofuse');
			$register = $cf->get('register');
			$version = $cf->get('version');
			$serverid = $cf->get('serverid');
			
			$provider = OCSXML::generate_providers($serverid,$name,$location,$termsofuse,$register, $ssl=false);
			
			$provider_path = ELoader::$prev_path.'/providers.xml';
			
			$stream = fopen($provider_path, 'w');
			fwrite($stream, $provider);
			fclose($stream);
			
			EStructure::view('wizard/step2save');
		}
			
		$serverid = EHeaderDataParser::post('serverid');
		$name = EHeaderDataParser::post('name');
		$host = EHeaderDataParser::post('host');
		$website = EHeaderDataParser::post('website');
		$location = EHeaderDataParser::post('location');
		$termsofuse = EHeaderDataParser::post('termsofuse');
		$register = EHeaderDataParser::post('register');
		$contact = EHeaderDataParser::post('contact');
		$ssl = EHeaderDataParser::post('ssl');
		$format = EHeaderDataParser::post('format');
		$version = OCSUser::version();
		
		//try to guess correct values
		if(empty($host)){
			$host = $_SERVER["SERVER_NAME"];
		}
		
		if(empty($website)){
			$website = EUtility::get_domain($host);
		}
		
		if(empty($name)){
			$name = ucfirst(EUtility::get_clear_domain($host)).' OCS Server';
		}
		
		if(empty($serverid)){
			$serverid = 'ocs_'.EUtility::get_clear_domain($host);
		}
		
		if(empty($location)){
			$location = 'http://'.ELoader::$root_path.'/v1/';
		}
		
		if($ssl=='yes'){
			$location = str_replace('http://','https://',$location);
		} else {
			$location = str_replace('https://','http://',$location);
		}
		
		//initialize everything to empty string
		$ssly = ''; $ssln = ''; $jsony = ''; $jsonn = '';
		//set the correct value for each menu
		if($ssl=='yes'){ $ssly = 'selected'; } else { $ssln = 'selected'; }
		if($format=='json'){ $jsony = 'selected'; } else { $jsonn = 'selected'; }
		
		$ocsserver_path = ELoader::$prev_path.'/config/ocsserver.conf.php';
				
		$cf = new EConfigFile();
		$cf->set_abs_file($ocsserver_path);
		
		$cf->set('name', $name);
		$cf->set('host', $host);
		$cf->set('website', $host);
		$cf->set('contact', $contact);
		$cf->set('location', $location);
		$cf->set('ssl', $ssl);
		$cf->set('format', $format);
		$cf->set('termsofuse', $termsofuse);
		$cf->set('register', $register);
		$cf->set('version', $version);
		$cf->set('serverid', $serverid);
		$cf->save();
		
		$data = array();
		$data['serverid'] = $serverid;
		$data['name'] = $name;
		$data['website'] = $website;
		$data['host'] = $host;
		$data['location'] = $location;
		$data['termsofuse'] = $termsofuse;
		$data['register'] = $register;
		$data['contact'] = $contact;
		$data['ssln'] = $ssln;
		$data['ssly'] = $ssly;
		$data['jsonn'] = $jsonn;
		$data['jsony'] = $jsony;
		$data['exampleprovider'] = htmlspecialchars(OCSXML::generate_providers($serverid,$name,$location,$termsofuse,$register, $ssl=false), ENT_QUOTES);
		
		//performing /v1/config get request
		$s = new ENetworkSocket($location);
		EUtility::hide_output();
		$c = $s->get('config');
		if(empty(EUtility::show_output())){
			$data['configcall'] = htmlspecialchars($c);
		} else {
			$data['configcall'] = 'We couldn\'t connect to OCS server. Check SSL settings and server location entries.';
		}
		EStructure::view('wizard/step2', $data);
		EStructure::view("footer");
	}
	
	public function step3()
	{
		if($this->arg_key('save')){
			$pass1 = EHeaderDataParser::post('pass');
			$pass2 = EHeaderDataParser::post('pass2');
			
			if($pass1==$pass2){
				$cf = new EConfigFile('generic');
		
				$cf->set('password', $pass1);
				$cf->set('enabled', 'protected');
				$cf->save();
			}
			EStructure::view('wizard/step3save');
		} else {
			$data = array();
			
			if(isset(EConfig::$data['generic']['password'])){
				$data['pass'] = EConfig::$data['generic']['password'];
			} else {
				$data['pass'] = '';
			}
			
			EStructure::view('wizard/step3', $data);
		}
	}
}

?>
