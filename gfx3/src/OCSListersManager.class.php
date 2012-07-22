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

class OCSLister{
	
	//variables
	private $table;
	private $datatable;
	//global instance
	public $main;
	
	
	public function __construct($data=""){
		//setting initial value for table
		if(!empty($data)){
			$this->table = $data;
			$this->datatable = new EData($this->table);
		} else {
			$this->set_table_search($this->table);
		}
		//setting main to global
		$this->main = EMain::getRef();
	}
	
	/*
	 * sets the database table name in which performs the search
	 */
	public function set_table_search($tbl){
		//assuring $data is safe to be executed on a db query
		$this->main->db->safe($data);
		//setting internal attribute
		if($this->main->db->table_exists($tbl)){
			$this->table = $tbl;
			$this->datatable = new EData($this->table);
		} else {
			ELog::error("$tbl does not exists on database.");
		}
	}
	
}

class OCSContentLister extends OCSLister {
	
	//variables
	private $table;
	private $datatable;
	//global instance
	public $main;
	
	//inheriting constructor
	public function __construct($data="ocs_content"){
		//setting initial value for table
		if(!empty($data)){
			$this->table = $data;
			$this->datatable = new EData($this->table);
		} else {
			$this->set_table_search($this->table);
		}
		//setting main to global
		global $main;
		$this->main = $main;
	}
	
	public function ocs_content_list($searchstr,$sortmode="new",$page=1,$pagesize=10,$user=""){
		
		if(empty($page)){ $page=1; }
		//setting dynamic page size
		$page = ($page-1)*($pagesize);
		switch($sortmode){
			case "new":
				$where = "ORDER BY id DESC LIMIT $page,$pagesize";
				break;
			case "alpha":
				$where = "ORDER BY name ASC LIMIT $page,$pagesize";
				break;
			case "high":
				$where = "ORDER BY score DESC LIMIT $page,$pagesize";
				break;
			case "down":
				$where = "ORDER BY downloads DESC LIMIT $page,$pagesize";
				break;
			default:
				$where = "ORDER BY id DESC LIMIT $page,$pagesize";
				break;
		}
		
		if(!empty($user)){
			$whereuser = " AND user = '$user' ";
		} else {
			$whereuser = "";
		}
		
		
		$r = $this->datatable->find("id,owner,votes,score,name,type,downloadname1,downloadlink1,version,summary","WHERE name LIKE '%$searchstr%' $whereuser $where");
		return $r;
	}
}

class OCSCommentLister extends OCSLister {
	
	//variables
	private $table;
	private $datatable;
	//global instance
	public $main;
	
	//inheriting constructor
	public function __construct($data="ocs_comment"){
		//setting initial value for table
		if(!empty($data)){
			$this->table = $data;
			$this->datatable = new EData($this->table);
		} else {
			$this->set_table_search($this->table);
		}
		//setting main to global
		global $main;
		$this->main = $main;
	}
	
	public function ocs_comment_list($type,$content,$content2,$page=1,$pagesize=10){
		
		if(empty($page)){ $page=1; }
		
		//setting dynamic page size
		$page = ($page-1)*($pagesize);
		$where = "ORDER BY c.id ASC LIMIT $page,$pagesize";
		
		$q = "SELECT * FROM ocs_comment AS c JOIN ocs_person AS p on c.owner = p.id WHERE c.content = $content $where";
		$r = $this->main->db->q($q);
		
		$result = array();
		while($row=mysql_fetch_assoc($r)){
			$result[]["id"] = $row["id"];
			$result[]["subject"] = $row["subject"];
			$result[]["text"] = $row["message"];
			$result[]["childcount"] = 0;
			$result[]["user"] = $row["login"];
			$result[]["date"] = $row["date"];
			$result[]["score"] = $row["score"];
		}
		
		return $result;
		
	}
}

class OCSFanLister extends OCSLister {
	
	//variables
	private $table;
	private $datatable;
	//global instance
	public $main;
	
	//inheriting constructor
	public function __construct($data="ocs_fan"){
		//setting initial value for table
		if(!empty($data)){
			$this->table = $data;
			$this->datatable = new EData($this->table);
		} else {
			$this->set_table_search($this->table);
		}
		//setting main to global
		global $main;
		$this->main = $main;
	}
	
	public function ocs_fan_list($content,$page=1,$pagesize=10){
		
		if(empty($page)){ $page=1; }
		
		//setting dynamic page size
		$page = ($page-1)*($pagesize);
		
		$person = $this->main->user->id();
		
		$q = "SELECT * FROM ocs_fan AS f JOIN ocs_person AS p on f.person = p.id WHERE f.person=$person LIMIT $page,$pagesize";
		$r = $this->main->db->q($q);
		
		$result = array();
		while($row=mysql_fetch_assoc($r)){
			$result[]["personid"] = $row["login"];
		}
		
		return $result;
		
	}
}

?>
