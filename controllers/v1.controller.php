<?php
/*
 * on this file gfx inclusion is useless as gfx is already running
 */

class V1Controller extends EController
{
	private  function _checkpassword($forceuser=true) {
			//valid user account ?
			if(isset($_SERVER['PHP_AUTH_USER'])) $authuser=$_SERVER['PHP_AUTH_USER']; else $authuser='';
			if(isset($_SERVER['PHP_AUTH_PW']))	 $authpw=$_SERVER['PHP_AUTH_PW']; else $authpw='';
			
			//this small (and dirty) hack checks if the client who requested the page is konqueror
			//which is also Qt itself
			//TODO: maybe fix this thing?
			if(isset($_SERVER['HTTP_USER_AGENT'])){
				$iskonqueror = stristr($_SERVER['HTTP_USER_AGENT'],"Konqueror");
			} else {
				$iskonqueror = false;
			}
			
			if(empty($authuser)) {
				if($forceuser){
					if(!$iskonqueror){
						header("WWW-Authenticate: Basic realm=\"Private Area\"");
						header('HTTP/1.0 401 Unauthorized');
						exit;
					} else {
						$txt=OCSXML::generatexml('','failed',999,'needs authentication');
						echo($txt);
						exit;
					}
				}else{
					$identifieduser='';
				}
			}else{
				/*
				$user=H01_USER::finduserbyapikey($authuser,CONFIG_USERDB);
				if($user==false) {
				*/
					$user=OCSUser::server_checklogin($authuser,$authpw);
					if($user==false) {
						if($forceuser){
							if(!$iskonqueror){
								header("WWW-Authenticate: Basic realm=\"Private Area\"");
								header('HTTP/1.0 401 Unauthorized');
								exit;
							} else {
								$txt=OCSXML::generatexml('','failed',999,'needs authentication');
								echo($txt);
								exit;
							}
						}else{
							$identifieduser='';
						}
					}else{
						$identifieduser=$user;
					}
					/*
				}else{
					$identifieduser=$user;
				}*/
			}
		return $identifieduser;
	}
	
	public function index()
	{
		$v1_config_url = EPageProperties::get_current_website_url()."/v1/config";
		
		echo "Hello! This webserver runs an Open Collaboration Services server.<br>";
		echo "Check <a href=\"$v1_config_url\">$v1_config_url</a> for configuring your OCS client.";
	}
	
    public function config()
    {
        //$user=$this->checkpassword(false);
		
		$xml['version']=EConfig::$data["ocsserver"]["version"];;
		$xml['website']=EConfig::$data["ocsserver"]["website"];
		$xml['host']=EConfig::$data["ocsserver"]["host"];;
		$xml['contact']=EConfig::$data["ocsserver"]["contact"];;
		$xml['ssl']=EConfig::$data["ocsserver"]["ssl"];;
		echo(OCSXML::generatexml('xml','ok',100,'',$xml,'config','',1));
    }
    
    public function person($args){
		if(empty($args)){
			echo(OCSXML::generatexml(EConfig::$data["ocsserver"]["format"],'failed',999,'malformed query'));
			return;
		}
		
		/*
		private  function personadd(EConfig::$data["ocsserver"]["format"],$login,$passwd,$firstname,$lastname,$email) {
		*/
		if($args[0]=='add'){
			$login = EHeaderDataParser::secure_post('login');
			$passwd = EHeaderDataParser::secure_post('password');
			$firstname = EHeaderDataParser::secure_post('firstname');
			$lastname = EHeaderDataParser::secure_post('lastname');
			$email = EHeaderDataParser::secure_post('email');
			//$user=$this->checkpassword(false);
			//$this->checktrafficlimit($user);

			if($login<>'' and $passwd<>'' and $firstname<>'' and $lastname<>'' and $email<>''){
				if(OCSUser::isvalidpassword($passwd)){
					if(OCSUser::isloginname($login)){
						if(!OCSUser::server_exists($login)){
							if(OCSUser::server_countusersbyemail($email)==0) {
								if(OCSUser::isvalidemail($email)) {
									OCSUser::server_register($login,$passwd,$firstname,$lastname,$email);
									echo(OCSXML::generatexml(EConfig::$data["ocsserver"]["format"],'ok',100,''));
								}else{
									echo(OCSXML::generatexml(EConfig::$data["ocsserver"]["format"],'failed',106,'email already taken'));
								}
							}else{
								echo(OCSXML::generatexml(EConfig::$data["ocsserver"]["format"],'failed',105,'email invalid'));
							}
						}else{
							echo(OCSXML::generatexml(EConfig::$data["ocsserver"]["format"],'failed',104,'login already exists'));
						}
					}else{
						echo(OCSXML::generatexml(EConfig::$data["ocsserver"]["format"],'failed',103,'please specify a valid login'));
					}
				}else{
					echo(OCSXML::generatexml(EConfig::$data["ocsserver"]["format"],'failed',102,'please specify a valid password'));
				}
			}else{
				echo(OCSXML::generatexml(EConfig::$data["ocsserver"]["format"],'failed',101,'please specify all mandatory fields'));
			}
		}
		
		if($args[0]=='check'){
			$login = EHeaderDataParser::secure_post('login');
			$passwd = EHeaderDataParser::secure_post('password');
			//$user=$this->checkpassword(false);
			//$this->checktrafficlimit($user);
			//OCSUser::server_load();
			
			if($login<>''){
				$reallogin=OCSUser::server_checklogin($login,$passwd); // $login,CONFIG_USERDB,$passwd,PERM_Login
				if($reallogin<>false){
					$xml['person']['personid']=$reallogin;
					echo(OCSXML::generatexml(EConfig::$data["ocsserver"]["format"],'ok',100,'',$xml,'person','check',2)); 
				}else{
						echo(OCSXML::generatexml(EConfig::$data["ocsserver"]["format"],'failed',102,'login not valid'));
				}
			}else{
				echo(OCSXML::generatexml(EConfig::$data["ocsserver"]["format"],'failed',101,'please specify all mandatory fields'));
			}
			
		}
		
		if($args[0]=='data'){
			if(isset($args[1])){ $username = $args[1]; } else { $username = ""; }
					
			if(empty($username)) {
				$user=$this->_checkpassword();
			}else{
				$user=$this->_checkpassword(false);
			}
			
			if(empty($username)){ $username=$user; }
			
			$DBuser = OCSUser::server_get_user_info($username);
			
			if($DBuser==false){
				$txt=OCSXML::generatexml(EConfig::$data["ocsserver"]["format"],'failed',101,'person not found');
				echo($txt);
			}else{
				if(isset($DBuser[0]) and is_array($DBuser[0])){
					$DBuser = $DBuser[0];
				}
				$xml=array();
				$xml[0]['personid']=$DBuser['login'];
				$xml[0]['firstname']=$DBuser['firstname'];
				$xml[0]['lastname']=$DBuser['lastname'];
				$xml[0]['email']=$DBuser['email'];
				//$xml[0]['description']=H01_UTIL::bbcode2html($DBuser['description']);
				
				$txt=OCSXML::generatexml(EConfig::$data["ocsserver"]["format"],'ok',100,'',$xml,'person','full',2);
				//$txt=$this->generatexml($format,'failed',102,'data is private');
				echo($txt);
			}

		}
		
	}
    
}

?>
