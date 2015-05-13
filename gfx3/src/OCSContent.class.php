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
	public $personid;
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
	public $license;
	public $preview1;
	public $preview2;
	public $preview3;
	
	private $ocs_content;
	private $data;
	
	
	/*
	 * Enabling main to be on a global context.
	 */
	public function __construct(){
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
			$this->personid = $r[0]["personid"];
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
			$this->license = $r[0]["license"];
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
		EDatabase::q("UPDATE ocs_content SET score=".$this->score.", votes=".$this->votes." WHERE id=".$this->id." LIMIT 1");
		
	}
	
	/*
	 * Add a download file to the current content.
	 */
	public function downloadadd(){
		if(!is_dir("content/".$this->id)){
			chdir("content");
			if(!mkdir($this->id)){
				ELog::error("<b>mkdir</b> failed for some reason. Inspect.");
				return false;
			}
			chdir("..");
		}
		$path = "content/".$this->id."/";
		
		//clean evetual additional files
		EFileSystem::clean_all_files_in($path,array("1","2","3"));
		
		//if upload file failed print error. Else add link to content object.
		if(!EFileSystem::move_uploaded_file_in_ext($path)){
			ELog::error("<b>get_uploaded_file</b> failed! Path: ($path) ");
			return false;
		} else {
			$this->downloadlink1 = "http://".EConfig::$data["ocs"]["host"]; //retrieve website running server
			$this->downloadlink1 .= "/content/".$this->id."/".EFileSystem::get_uploaded_file_name();
			$this->downloadname1 = EFileSystem::get_uploaded_file_name();
			EDatabase::q("UPDATE ocs_content SET downloadlink1='".$this->downloadlink1."', downloadname1='".$this->downloadname1."' WHERE id=".$this->id." LIMIT 1");
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
				ELog::error("<b>mkdir</b> failed for some reason. Inspect.");
				return false;
			}
			chdir("..");
		}
		$path = "content/".$this->id."/";
		
		//if upload file failed print error. Else add link to content object.
		//if(!EFileSystem::move_uploaded_file_in($path,$preview)){
		if(!EFileSystem::move_uploaded_file_in($path,$preview)){
			ELog::error("<b>get_uploaded_file</b> failed! Path: ($path) ");
			return false;
		} else {
			switch($preview){
				case 1:
					$this->preview1 = "http://".EConfig::$data["ocs"]["host"]; //retrieve website running server
					$this->preview1 .= "/content/".$this->id."/".$preview;
					$previewlink = $this->preview1;
					break;
				case 2:
					$this->preview2 = "http://".EConfig::$data["ocs"]["host"]; //retrieve website running server
					$this->preview2 .= "/content/".$this->id."/".$preview;
					$previewlink = $this->preview2;
					break;
				case 3:
					$this->preview3 = "http://".EConfig::$data["ocs"]["host"]; //retrieve website running server
					$this->preview3 .= "/content/".$this->id."/".$preview;
					$previewlink = $this->preview3;
					break;
			}
			EDatabase::q("UPDATE ocs_content SET preview".$preview."='".$previewlink."' WHERE id=".$this->id." LIMIT 1");
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
			EDatabase::q("UPDATE ocs_content SET downloadlink1='' WHERE id=".$this->id." LIMIT 1");
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
			EDatabase::q("UPDATE ocs_content SET preview$preview='' WHERE id=".$this->id." LIMIT 1");
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
	
	public function license(){
		return $this->license;
	}
	
	/*
	 * Saving the ram rapresentation into memory (database).
	 */
	public function save(){
		//TODO: implement unique name.
		
		//saving
		EDatabase::q("INSERT INTO ocs_content (name,type,owner,personid,downloadname1,downloadlink1,description,summary,version,changelog,preview1,preview2,preview3,license) VALUES ('".$this->name."',".$this->type.",".$this->owner.",'".$this->personid."','".$this->downloadname1."','".$this->downloadlink1."','".$this->description."','".$this->summary."','".$this->version."','".$this->changelog."','".$this->preview1."','".$this->preview2."','".$this->preview3."',".$this->license.")");
		//updating new id, got from database
		$this->id = $id = EDatabase::last_insert_id();
	}
	
	/*
	 * Automatic update of ocs_table.
	 */
	public function update(){
		$this->ocs_content->update("id=".$this->id);
	}
	
	public function delete(){
		//EDatabase::q("DELETE FROM ocs_content WHERE id=".$this->id." LIMIT 1");
		EDatabase::q("DELETE FROM ocs_content WHERE id=".$this->id." LIMIT 1");
		EDatabase::q("DELETE FROM ocs_comment WHERE content=".$this->id."<br>");
		EFileSystem::rmdir("content/".$this->id);
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
		EDatabase::safe($data);
		//data validations
		if(!isset($data['type'])){ ELog::error("OCSContent: type not defined. Mandatory field."); } else { $this->type = $data['type']; }
		if(!isset($data['name'])){ ELog::error("OCSContent: name not defined. Mandatory field."); } else { $this->name = $data['name']; }
		if(!isset($data['personid'])){ ELog::error("OCSContent: personid not defined. Mandatory field."); } else { $this->personid = $data['personid']; }
		if(!isset($data['downloadname1'])){ $this->downloadname1 = ""; } else { $this->downloadname1 = $data['downloadname1']; }
		if(!isset($data['downloadlink1'])){ $this->downloadlink1 = ""; } else { $this->downloadlink1 = $data['downloadlink1']; }
		if(!isset($data['description'])){ $this->description = ""; } else { $this->description = $data['description']; }
		if(!isset($data['summary'])){ $this->summary = ""; } else { $this->summary = $data['summary']; }
		if(!isset($data['version'])){ $this->version = ""; } else { $this->version = $data['version']; }
		if(!isset($data['changelog'])){ $this->changelog = ""; } else { $this->changelog = $data['changelog']; }
		if(!isset($data['preview1'])){ $this->preview1 = ""; } else { $this->preview1 = $data['preview1']; }
		if(!isset($data['preview2'])){ $this->preview2 = ""; } else { $this->preview2 = $data['preview2']; }
		if(!isset($data['preview3'])){ $this->preview3 = ""; } else { $this->preview3 = $data['preview3']; }
		if(!isset($data['license'])){ $this->license = ""; } else { $this->license = $data['license']; }
	}
	
	/*
	 * This function returns the associated id for the selected content
	 */
	public function id(){
		return $this->id;
	}
	
}


?>
