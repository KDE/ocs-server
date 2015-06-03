<?php
/*
 * on this file gfx inclusion is useless as gfx is already running
 */

class V1Controller extends EController
{
	
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
			
		}
	}
    
}

?>
