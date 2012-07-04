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
	public $preview1;
	public $preview2;
	public $preview3;
	
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
			$this->preview1 = $r[0]["preview1"];
			$this->preview2 = $r[0]["preview2"];
			$this->preview3 = $r[0]["preview3"];
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
	 * Add a download file to the current content.
	 */
	public function downloadadd(){
		if(!is_dir("content/".$this->id)){
			chdir("content");
			if(!mkdir($this->id)){
				$this->main->log->error("<b>mkdir</b> failed for some reason. Inspect.");
				return false;
			}
			chdir("..");
		}
		$path = "content/".$this->id."/";
		//if upload file failed print error. Else add link to content object.
		
		if(!EFileSystem::move_uploaded_file_in($path)){
			$this->main->log->error("<b>get_uploaded_file</b> failed! Path: ($path) ");
			return false;
		} else {
			$this->downloadlink1 = EPageProperties::get_current_website_url(); //retrieve website running server
			$this->downloadlink1 .= "/content/".$this->id."/".EFileSystem::get_uploaded_file_name();
			$this->main->db->q("UPDATE ocs_content SET downloadlink1='".$this->downloadlink1."' WHERE id=".$this->id." LIMIT 1");
			return true;
		}
	}
	
	/*
	 * Add a preview file to the current content.
	 */
	public function previewadd($content,$localfile,$preview){
		if(!is_dir("content/".$this->id)){
			chdir("content");
			if(!mkdir($this->id)){
				$this->main->log->error("<b>mkdir</b> failed for some reason. Inspect.");
				return false;
			}
			chdir("..");
		}
		$path = "content/".$this->id."/"; 
		
		//if upload file failed print error. Else add link to content object.
		//if(!EFileSystem::move_uploaded_file_in($path,$preview)){
		if(!EFileSystem::move_uploaded_file_in($path,$preview)){
			$this->main->log->error("<b>get_uploaded_file</b> failed! Path: ($path) ");
			return false;
		} else {
			switch($preview){
				case 1:
					$this->preview1 = EPageProperties::get_current_website_url(); //retrieve website running server
					$this->preview1 .= "/content/".$this->id."/".$preview.".".EFileSystem::get_file_extension(EFileSystem::get_uploaded_file_name());
					$previewlink = $this->preview1;
					break;
				case 2:
					$this->preview2 = EPageProperties::get_current_website_url(); //retrieve website running server
					$this->preview2 .= "/content/".$this->id."/".$preview.".".EFileSystem::get_file_extension(EFileSystem::get_uploaded_file_name());
					$previewlink = $this->preview2;
					break;
				case 3:
					$this->preview3 = EPageProperties::get_current_website_url(); //retrieve website running server
					$this->preview3 .= "/content/".$this->id."/".$preview.".".EFileSystem::get_file_extension(EFileSystem::get_uploaded_file_name());
					$previewlink = $this->preview3;
					break;
			}
			$this->main->db->q("UPDATE ocs_content SET preview".$preview."='".$previewlink."' WHERE id=".$this->id." LIMIT 1");
			return true;
		}
	}
	
	/*
	 * Delete current download file, if setted.
	 */
	public function downloaddelete(){
		$filename = explode("/", $this->downloadlink1);
		$filename = $filename[count($filename)-1];
		
		$path = "content/".$this->id."/".$filename;
		//if upload file failed print error. Else add link to content object.
		if(file_exists($path)){
			unlink($path);
			$this->downloadlink1 = "";
			$this->main->db->q("UPDATE ocs_content SET downloadlink1='' WHERE id=".$this->id." LIMIT 1");
		}
	}
	
	/*
	 * Delete current preview file, if setted.
	 */
	public function previewdelete($content,$preview){
		switch($preview){
			case 1:
				$this->prelink = $this->preview1;
				$this->preview1 = "";
				break;
			case 2:
				$this->prelink = $this->preview2;
				$this->preview2 = "";
				break;
			case 3:
				$this->prelink = $this->preview3;
				$this->preview3 = "";
				break; 
		}
		$filename = explode("/", $this->prelink);
		$filename = $filename[count($filename)-1]; 
		
		$path = "content/".$this->id."/".$filename;
		//if upload file failed print error. Else add link to content object.
		if(file_exists($path)){
			unlink($path);
			$this->main->db->q("UPDATE ocs_content SET preview$preview='' WHERE id=".$this->id." LIMIT 1");
		}
	}
	
	/*
	 * Returns a boolean if $preview is currently available or not.
	 */
	public function is_preview_available($preview){
		switch($preview){
			case 1:
				if(empty($this->preview1)){ return false; } else { return true; }
			case 2:
				if(empty($this->preview2)){ return false; } else { return true; }
			case 3:
				if(empty($this->preview3)){ return false; } else { return true; }
		}
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
		$this->main->db->q("INSERT INTO ocs_content (name,type,owner,downloadname1,downloadlink1,description,summary,version,changelog,preview1,preview2,preview3) VALUES ('".$this->name."',".$this->type.",".$this->owner.",'".$this->downloadname1."','".$this->downloadlink1."','".$this->description."','".$this->summary."','".$this->version."','".$this->changelog."','".$this->preview1."','".$this->preview2."','".$this->preview3."')");
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
		if(!isset($data['type'])){ $this->main->log->error("OCSContent: type not defined. Mandatory field."); } else { $this->type = $data['type']; }
		if(!isset($data['name'])){ $this->main->log->error("OCSContent: name not defined. Mandatory field."); } else { $this->name = $data['name']; }
		if(!isset($data['downloadname1'])){ $this->downloadname1 = ""; } else { $this->downloadname1 = $data['downloadname1']; }
		if(!isset($data['downloadlink1'])){ $this->downloadlink1 = ""; } else { $this->downloadlink1 = $data['downloadlink1']; }
		if(!isset($data['description'])){ $this->description = ""; } else { $this->description = $data['description']; }
		if(!isset($data['summary'])){ $this->summary = ""; } else { $this->summary = $data['summary']; }
		if(!isset($data['version'])){ $this->version = ""; } else { $this->version = $data['version']; }
		if(!isset($data['changelog'])){ $this->changelog = ""; } else { $this->changelog = $data['changelog']; }
		if(!isset($data['preview1'])){ $this->preview1 = ""; } else { $this->preview1 = $data['preview1']; }
		if(!isset($data['preview2'])){ $this->preview2 = ""; } else { $this->preview2 = $data['preview2']; }
		if(!isset($data['preview3'])){ $this->preview3 = ""; } else { $this->preview3 = $data['preview3']; }
	}
	
	/*
	 * This function returns the associated id for the selected content
	 */
	public function id(){
		return $this->id;
	}
	
}


?>
