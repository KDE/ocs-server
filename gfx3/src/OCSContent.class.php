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
	
	public $id;
	public $owner;
	public $name;
	public $type;
	public $description;
	public $summary;
	public $version;
	public $changelog;
	public $downloadname1;
	public $downloadlink1;
	public $votes;
	public $score;
	
	private $ocs_content;
	private $data;
	private $main;
	
	
	/*
	 * Enabling main to be on a global context.
	 */
	public function __construct(){
		global $main;
		$this->main = $main;
		$this->ocs_content = new EData("ocs_content");
	}
	
	/*
	 * Load into memory a content from table ocs_content
	 */
	public function load($id){
		if($this->ocs_content->is_there("id","id=$id")) {
			$r = $this->ocs_content->find("*", "where id=$id LIMIT 1");
			$this->id = $id;
			$this->owner = $r[0]["owner"];
			$this->votes = $r[0]["votes"];
			$this->score = $r[0]["score"];
			$this->name = $r[0]["name"];
			$this->type = $r[0]["type"];
			$this->description = $r[0]["description"];
			$this->summary = $r[0]["summary"];
			$this->version = $r[0]["version"];
			$this->changelog = $r[0]["changelog"];
			$this->downloadname1 = $r[0]["downloadname1"];
			$this->downloadlink1 = $r[0]["downloadlink1"];
			$this->votes = $r[0]["votes"];
			$this->score = $r[0]["score"];
			return true;
		} else {
			return false;
		}
	}
	
	/*
	 * Set a weighted score to the content.
	 */
	public function set_score($score){
		//acquiring data
		$oldmedia = $this->score;
		$newscore = $score;
		
		$oldvotes = $this->votes;
		$newvotes = $this->votes + 1;
		//calculating new media
		$newmedia = ($oldmedia * $oldvotes + $newscore) / ($newvotes);
		
		//setting new infos to local memory object
		$this->score = $newmedia;
		$this->votes = $newvotes;
		
		//updating db
		$this->main->db->q("UPDATE ocs_content SET score=".$this->score.", votes=".$this->votes." WHERE id=".$this->id." LIMIT 1");
		
	}
	
	/*
	 * Checks if the current loaded content is owned by $id
	 */
	public function is_owned($id){
		if($this->owner == $id){
			return true;
		} else {
			return false;
		}
	}
	
	/*
	 * Return the number of people who has performed a vote action on this object.
	 */
	public function votes(){
		return $this->votes;
	}
	
	/*
	 * Returns the weighted score for this object.
	 */
	public function score(){
		return $this->score;
	}
	
	/*
	 * Saving the ram rapresentation into memory (database).
	 */
	public function save(){
		//TODO: implement unique name.
		
		//saving
		$this->main->db->q("INSERT INTO ocs_content (name,type,owner,downloadname1,downloadlink1,description,summary,version,changelog) VALUES ('".$this->name."',".$this->type.",".$this->owner.",'".$this->downloadname1."','".$this->downloadlink1."','".$this->description."','".$this->summary."','".$this->version."','".$this->changelog."')");
		//updating new id, got from database
		$r = $this->main->db->q("SELECT id FROM ocs_content where name='".$this->name."' and owner=".$this->owner." LIMIT 1");
		$this->id = $r[0]["id"];
	}
	
	/*
	 * Automatic update of ocs_table.
	 */
	public function update(){
		$this->ocs_content->update("id=".$this->id);
	}
	
	public function delete(){
		$this->main->db->q("DELETE FROM ocs_content WHERE id=".$this->id." LIMIT 1");
	}
	
	/*
	 * Setting id of the owner of the content.
	 */
	public function set_owner($owner){
		$this->owner = $owner;
	}
	
	/*
	 * Manually setting internal data.
	 */
	public function set_data($data){
		// assuring those are not evil data to be used as SQL injections
		$this->main->db->safe($data);
		//data validations
		if(!isset($data['type'])){ $this->main->elog->error("OCSContent: type not defined. Mandatory field."); } else { $this->type = $data['type']; }
		if(!isset($data['name'])){ $this->main->elog->error("OCSContent: name not defined. Mandatory field."); } else { $this->name = $data['name']; }
		if(!isset($data['downloadname1'])){ $this->downloadname1 = ""; } else { $this->downloadname1 = $data['downloadname1']; }
		if(!isset($data['downloadlink1'])){ $this->downloadlink1 = ""; } else { $this->downloadlink1 = $data['downloadlink1']; }
		if(!isset($data['description'])){ $this->description = ""; } else { $this->description = $data['description']; }
		if(!isset($data['summary'])){ $this->summary = ""; } else { $this->summary = $data['summary']; }
		if(!isset($data['version'])){ $this->version = ""; } else { $this->version = $data['version']; }
		if(!isset($data['changelog'])){ $this->changelog = ""; } else { $this->changelog = $data['changelog']; }
	}
	
	/*
	 * This function returns the associated id for the selected content
	 */
	public function id(){
		return $this->id;
	}
	
}


?>
