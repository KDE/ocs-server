<?php
/*
 * on this file gfx inclusion is useless as gfx is already running
 */

class V1Controller extends EController
{
	
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
    
}

?>
