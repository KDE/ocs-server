<?php

/*
 *   TRT GFX 4.0.0
 * 
 *   support:	happy.snizzo@gmail.com
 *   website:	https://projects.kde.org/projects/playground/www/ocs-server
 *   credits:	Claudio Desideri
 *   
 *   This software is released under the GPLv3 License.
 *   http://opensource.org/licenses/GPL-3.0
 */ 


/*
 * Static class used as a helper for managing xml output in
 * order to be OCS compliant.
 */
class OCSXML{
	
	/*
	 * Function originally ported from reference implementation.
	 * Useful to print server generated OCS server compliant xml.
	 * 
	 * Example:
	 * 
	   $xml['version']=EConfig::$data["ocsserver"]["version"];;
       $xml['website']=EConfig::$data["ocsserver"]["website"];
	   $xml['host']=EConfig::$data["ocsserver"]["host"];;
	   $xml['contact']=EConfig::$data["ocsserver"]["contact"];;
	   $xml['ssl']=EConfig::$data["ocsserver"]["ssl"];;
	   echo(OCSXML::generatexml($format,'ok',100,'',$xml,'config','',1));
	 * 
	 */
	static public function generatexml($format,$status,$statuscode,$message,$data=array(),$tag='',$tagattribute='',$dimension=-1,$itemscount='',$itemsperpage='') {
		if($format=='json') {

			$json=array();
			$json['status']=$status;
			$json['statuscode']=$statuscode;
			$json['message']=$message;
			$json['totalitems']=$itemscount;
			$json['itemsperpage']=$itemsperpage;
			$json['data']=$data;
			return(json_encode($json));


		}else{
			$txt='';
			$writer = xmlwriter_open_memory();
			xmlwriter_set_indent( $writer, 2 );
			xmlwriter_start_document($writer );
			xmlwriter_start_element($writer,'ocs');
			xmlwriter_start_element($writer,'meta');
			xmlwriter_write_element($writer,'status',$status);
			xmlwriter_write_element($writer,'statuscode',$statuscode);
			xmlwriter_write_element($writer,'message',$message);
			if($itemscount<>'') xmlwriter_write_element($writer,'totalitems',$itemscount);
			if(!empty($itemsperpage)) xmlwriter_write_element($writer,'itemsperpage',$itemsperpage);
			xmlwriter_end_element($writer);
			//echo($dimension);
			if($dimension=='0') {
				// 0 dimensions
				xmlwriter_write_element($writer,'data',$data);

			}elseif($dimension=='1') {
				xmlwriter_start_element($writer,'data');
				foreach($data as $key=>$entry) {
					xmlwriter_write_element($writer,$key,$entry);
				}
				xmlwriter_end_element($writer);

			}elseif($dimension=='2') {
				xmlwriter_start_element($writer,'data');
				if(!empty($data)){
					foreach($data as $entry) {
						xmlwriter_start_element($writer,$tag);
						if(!empty($tagattribute)) {
							xmlwriter_write_attribute($writer,'details',$tagattribute);
						}
						foreach($entry as $key=>$value) {
							if(is_array($value)){
								foreach($value as $k=>$v) {
									xmlwriter_write_element($writer,$k,$v);
								}
							} else {
								xmlwriter_write_element($writer,$key,$value);
							}
						}
						xmlwriter_end_element($writer);
					}
				}
				xmlwriter_end_element($writer);

			}elseif($dimension=='3') {
				xmlwriter_start_element($writer,'data');
				foreach($data as $entrykey=>$entry) {
					xmlwriter_start_element($writer,$tag);
					if(!empty($tagattribute)) {
						xmlwriter_write_attribute($writer,'details',$tagattribute);
					}
					foreach($entry as $key=>$value) {
						if(is_array($value)){
							xmlwriter_start_element($writer,$entrykey);
							foreach($value as $k=>$v) {
								xmlwriter_write_element($writer,$k,$v);
							}
							xmlwriter_end_element($writer);
						} else {
							xmlwriter_write_element($writer,$key,$value);
						}
					}
					xmlwriter_end_element($writer);
				}
				xmlwriter_end_element($writer);
			}elseif($dimension=='dynamic') {
				xmlwriter_start_element($writer,'data');
				//$this->toxml($writer,$data,'comment');
				if(is_array($data)) OCSXML::toxml($writer,$data,$tag);
				xmlwriter_end_element($writer);
			}

			xmlwriter_end_element($writer);

			xmlwriter_end_document( $writer );
			$txt.=xmlwriter_output_memory( $writer );
			unset($writer);
			return($txt);
		}
	}
	
	/**
	 * Take an array of any size, and make it into xml
	 * @param xmlwriter		An xmlwriter instance
	 * @param array			The array which is to be transformed
	 * @param mixed			Either a string, or an array of elements defining element names for each level in the XML hierarchy
	 *						In the case of multiple lists of differently titled items at the same level, adding an array inside the array will allow for this to be constructed.
	 * @param int			Internal use (the index of the child item in question - corresponds to the index in the second level array above)
	 */
	static public function toxml($writer,$data,$node,$childindex=0) {
		$nodename=$node;
		if(is_array($node)){
			$nodename=array_shift($node);
		}

		$childcount=-1;
		foreach($data as $key => $value) {
			$childcount++;
			if (is_numeric($key)) {
				if(is_array($nodename)) {
					$key = $nodename[$childindex];
				} else {
					$key = $nodename;
				}
			}
			if (is_array($value)){
				xmlwriter_start_element($writer,$key);
				OCSXML::toxml($writer,$value,$node,$childcount);
				xmlwriter_end_element($writer);
			}else{
				xmlwriter_write_element($writer,$key,$value);
			}
		}
		if(is_array($node)) {
			array_unshift($node,$nodename);
		}
	}
	/*
	 * Generate a providers.xml file for clients.
	 * If arguments are empty data will be auto-generated.
	 */
	public static function generate_providers($serverid='',$name='',$location='',$termsofuse='',$register='', $ssl=false)
	{
		$version = EConfig::$data['ocsserver']['version'];
		
		$modules = array();
		
		//preconfigured for future modules
		if(class_exists("OCSUser")){ $modules[] = "person"; } //TODO: OCSUser should become OCSPerson
		if(class_exists("OCSFriend")){ $modules[] = "friend"; }
		if(class_exists("OCSMessage")){ $modules[] = "message"; }
		if(class_exists("OCSActivity")){ $modules[] = "activity"; }
		if(class_exists("OCSContent")){ $modules[] = "content"; }
		if(class_exists("OCSFan")){ $modules[] = "fan"; }
		if(class_exists("OCSKnowledgebase")){ $modules[] = "knowledgebase"; }
		if(class_exists("OCSEvent")){ $modules[] = "event"; }
		
		$xml = '';
		
		EUtility::hide_output();
		$xml .= "<providers>
			<provider>
				<id>$serverid</id>
				<location>$location</location>
				<name>$name</name>
				<icon/>
				<termsofuse>$termsofuse</termsofuse>
				<register>$register</register>
				<services>\n";
					foreach($modules as $module){
						$xml.= "\t\t\t\t<$module ocsversion=\"$version\"/>\n";
					}
				$xml .= "\t\t\t\t</services>
			</provider>
		</providers>";
		
		return $xml;
	}
}
?>	
