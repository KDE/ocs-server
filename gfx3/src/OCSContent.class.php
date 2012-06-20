<?php

/*
 *   TRT GFX 3.0.1 (beta build) BackToSlash
 * 
 *   support:	happy.snizzo@gmail.com
 *   website:	http://trt-gfx.googlecode.com
 *   credits:	Claudio Desideri
 *   
 *   This software is released under the MIT License.
 *   http://opensource.org/licenses/mit-license.php
 */ 


class OCSContent{
	
	private $data;
	private $main;
	private $id;
	private $owner;
	
	private $name;
	private $type;
	private $description;
	private $summary;
	private $version
	private $changelog;
	private $downloadname1;
	private $downloadlink1;
	
	public function __construct(){
		global $main;
		$this->main = $main;
	}
	
	/*
	 * Saving the ram rapresentation into memory (database).
	 */
	public function save(){
		$this->main->db->q("INSERT INTO ocs_content (name,type,downloadname1,downloadlink1,summary,version,changelog) VALUES ('".$this->downloadname1."','".$this->downloadlink1."','".$this->description."','".$this->summary."','".$this->version."','".$this->changelog."')");
	}
	
	/*
	 * Setting id of the owner of the content.
	 */
	public function setOwner($owner){
		$this->owner = $owner;
	}
	
	/*
	 * Manually setting internal data.
	 */
	public function setData($data){
		if(!isset($data['downloadname1'])){ $this->downloadname1 = ""; } else { $this->downloadname1 = $data['downloadname1']; }
		if(!isset($data['downloadlink1'])){ $this->downloadlink1 = ""; } else { $this->downloadlink1 = $data['downloadlink1']; }
		if(!isset($data['description'])){ $this->description = ""; } else { $this->description = $data['description']; }
		if(!isset($data['summary'])){ $this->summary = ""; } else { $this->summary = $data['summary']; }
		if(!isset($data['version'])){ $this->version = ""; } else { $this->version = $data['version']; }
		if(!isset($data['changelog'])){ $this->changelog = ""; } else { $this->changelog = $data['changelog']; }
	}
	
}


?>
