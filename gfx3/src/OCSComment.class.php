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


class OCSComment{
	
	public $id;
	public $owner;
	public $type;
	public $content;
	public $content2;
	public $parent;
	public $votes;
	public $score;
	public $subject;
	public $message;
	
	
	private $ocs_comment;
	private $data;
	private $main;
	
	
	/*
	 * Enabling main to be on a global context.
	 */
	public function __construct(){
		$this->main = EMain::getRef();
		$this->ocs_comment = new EData("ocs_comment");
	}
	
	/*
	 * Load into memory a content from table ocs_content
	 */
	public function load($id){
		if($this->ocs_comment->is_there("id","id=$id")) {
			$r = $this->ocs_comment->find("*", "where id=$id LIMIT 1");
			$this->id = $id;
			$this->type = $r[0]["type"];
			$this->owner = $r[0]["owner"];
			$this->content = $r[0]["content"];
			$this->content2 = $r[0]["content2"];
			$this->parent = $r[0]["parent"];
			$this->votes = $r[0]["votes"];
			$this->score = $r[0]["score"];
			$this->subject = $r[0]["subject"];
			$this->message = $r[0]["message"];
			$this->date = $r[0]["date"];
			return true;
		} else {
			return false;
		}
	}
	
	/*
	 * Checks if the current loaded comment is owned by $id
	 */
	public function is_owned($id){
		if($this->owner == $id){
			return true;
		} else {
			return false;
		}
	}
	
	/*
	 * This function returns the associated id for the selected content
	 */
	public function id(){
		return $this->id;
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
	public function save_to_db(){
		//TODO: implement unique name.
		
		//saving
		$this->main->db->q("INSERT INTO ocs_comment (type,owner,content,content2,parent,votes,score,subject,message) VALUES ('".$this->type."',".$this->owner.",".$this->content.",'".$this->content2."','".$this->parent."','".$this->votes."','".$this->score."','".$this->subject."','".$this->message."')");
		$this->id = $id = $this->main->db->last_insert_id();
		return $id;
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
		if(!isset($data['type'])){ $this->type = 1; } else { $this->type = $data['type']; }
		if(!isset($data['owner'])){ $this->owner = 0; /* anonymous */ } else { $this->owner = $data['owner']; }
		if(!isset($data['content'])){ $this->content = ""; } else { $this->content = $data['content']; }
		if(!isset($data['content2'])){ $this->content2 = ""; } else { $this->content2 = $data['content2']; }
		if(!isset($data['parent'])){ $this->parent = ""; } else { $this->parent = $data['parent']; }
		if(!isset($data['votes'])){ $this->votes = ""; } else { $this->votes = $data['votes']; }
		if(!isset($data['score'])){ $this->score = ""; } else { $this->score = $data['score']; }
		if(!isset($data['subject'])){ $this->subject = ""; } else { $this->subject = $data['subject']; }
		if(!isset($data['message'])){ $this->message = ""; } else { $this->message = $data['message']; }
		if(!isset($data['data'])){ $this->data = date(" j/n/Y H:i:s "); }
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
		$this->main->db->q("UPDATE ocs_comment SET score=".$this->score.", votes=".$this->votes." WHERE id=".$this->id." LIMIT 1");
		
	}
	
}


?>
