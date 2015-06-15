<?php

/*
 *   GFX 4
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
	
	
	public function __construct($data=""){
		//setting initial value for table
		if(!empty($data)){
			$this->table = $data;
			$this->datatable = new EModel($this->table);
		} else {
			$this->set_table_search($this->table);
		}
	}
	
	/*
	 * sets the database table name in which performs the search
	 */
	public function set_table_search($tbl){
		//assuring $data is safe to be executed on a db query
		EDatabase::safe($data);
		//setting internal attribute
		if(EDatabase::table_exists($tbl)){
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
	
	//inheriting constructor
	public function __construct($data="ocs_content"){
		parent::__construct($data);
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
		
		//TODO: move this into parent class constructor
		// or better: inspect why datatable isn't initialized by constructor
		if(is_null($this->datatable)){
			$this->datatable = new EModel("ocs_content");
		}
		
		$r = $this->datatable->find("id,owner,personid,description,changelog,preview1,votes,score,name,type,downloadname1,downloadlink1,version,summary,license","WHERE name LIKE '%$searchstr%' $whereuser $where");
		return $r;
	}
}

class OCSPersonLister extends OCSLister {
	
	//variables
	private $table;
	private $datatable;
	
	//inheriting constructor
	public function __construct($data="ocs_person"){
		parent::__construct($data);
	}
	
	public function ocs_person_search($username, $page=1,$pagesize=10){
		if(empty($page)){ $page=1; }
		
		//TODO: move this into parent class constructor
		// or better: inspect why datatable isn't initialized by constructor
		if(is_null($this->datatable)){
			$this->datatable = new EModel("ocs_person");
		}
		//setting dynamic page size
		$page = ($page-1)*($pagesize);
		
		$r = $this->datatable->find("id,login,firstname,lastname,email","WHERE login LIKE '%$username%'");
		return $r;
	}
}

class OCSCommentLister extends OCSLister {
	
	//variables
	private $table;
	private $datatable;
	
	//inheriting constructor
	public function __construct($data="ocs_comment"){
		parent::__construct($data);
	}
	
	public function ocs_comment_list($type,$content,$content2,$page=1,$pagesize=10){
		
		if(empty($page)){ $page=1; }
		
		//setting dynamic page size
		$page = ($page-1)*($pagesize);
		$where = "ORDER BY c.id ASC LIMIT $page,$pagesize";
		
		$q = "SELECT * FROM ocs_comment AS c JOIN ocs_person AS p on c.owner = p.id WHERE c.content = $content $where";
		$r = EDatabase::q($q);
		
		$result = array();
		$i = 0;
		while($row=mysqli_fetch_assoc($r)){
			$result[$i]["id"] = $row["id"];
			$result[$i]["subject"] = $row["subject"];
			$result[$i]["text"] = $row["message"];
			$result[$i]["childcount"] = 0;
			$result[$i]["user"] = $row["login"];
			$result[$i]["date"] = $row["date"];
			$result[$i]["score"] = $row["score"];
			$i += 1;
		}
		
		return $result;
		
	}
}
/*
class OCSPersonLister extends OCSLister {
    
    //variables
    private $table;
    private $datatable;
    
    //inheriting constructor
    public function __construct($data="ocs_person"){
        //setting initial value for table
        if(!empty($data)){
            $this->table = $data;
            $this->datatable = new EData($this->table);
        } else {
            $this->set_table_search($this->table);
        }
    }
    
    public function ocs_person_search($username,$page=1,$pagesize=10){
        
        if(empty($page)){ $page=1; }
        
        //setting dynamic page size
        $page = ($page-1)*($pagesize);
        $where = " LIMIT $page,$pagesize";
        
        $q = "SELECT * FROM ocs_person WHERE login LIKE '%$username%' $where";
        $r = EDatabase::q($q);
        
        $result = array();
        $i = 0;
        while($row=mysqli_fetch_assoc($r)){
            $result[$i]["personid"] = $row["login"];
            $result[$i]["firstname"] = $row["firstname"];
            $result[$i]["lastname"] = $row["lastname"];
            $i += 1;
        }
        
        return $result;
        
    }
}
*/
class OCSFanLister extends OCSLister {
	
	//variables
	private $table;
	private $datatable;
	//global instance
	public $main;
	
	//inheriting constructor
	public function __construct($data="ocs_fan"){
		parent::__construct($data);
	}
	
	public function ocs_fan_list($content,$page=1,$pagesize=10){
		
		if(empty($page)){ $page=1; }
		
		//setting dynamic page size
		$page = ($page-1)*($pagesize);
		
		$person = OCSUser::id();
		
		$q = "SELECT * FROM ocs_fan AS f JOIN ocs_person AS p on f.person = p.id WHERE f.person=$person LIMIT $page,$pagesize";
		$r = EDatabase::q($q);
		
		$result = array();
		while($row=mysqli_fetch_assoc($r)){
			$result[]["personid"] = $row["login"];
		}
		
		return $result;
		
	}
}

class OCSFriendsLister extends OCSLister {
    
    //variables
    private $table;
    private $datatable;
    //global instance
    public $main;
    
    //inheriting constructor
    public function __construct($data="ocs_friendship"){
        //setting initial value for table
        if(!empty($data)){
            $this->table = $data;
            $this->datatable = new EData($this->table);
        } else {
            $this->set_table_search($this->table);
        }
    }
    
    public function ocs_friend_list($fromuser,$page=1,$pagesize=10){
        
        if(empty($page)){ $page=1; }
        
        //setting dynamic page size
        $page = ($page-1)*($pagesize);
        
        $q = "SELECT * FROM ocs_friendship AS f JOIN ocs_person AS p on (f.id1 = p.id) OR (f.id2 = p.id) WHERE p.login!='$fromuser' LIMIT $page,$pagesize";
        $r = EDatabase::q($q);
        
        $result = array();
        while($row=mysqli_fetch_assoc($r)){
            $result[]["personid"] = $row["login"];
        }
        
        return $result;
    }
    
    public function ocs_sentinvitations($page=1,$pagesize=10){
        $id = OCSUser::id();
        
        if(empty($page)){ $page=1; }
        
        //setting dynamic page size
        $page = ($page-1)*($pagesize);

        $q = "SELECT * FROM ocs_friendinvitation AS f JOIN ocs_person AS p ON f.touser = p.id WHERE f.fromuser=$id LIMIT $page,$pagesize";
        $r = EDatabase::q($q);
        
        $result = array();
        while($row=mysqli_fetch_assoc($r)){
            $result[]["personid"] = $row["login"];
        }
        
        return $result;
    }
    
    public function ocs_receivedinvitations($page=1,$pagesize=999){
        $id = OCSUser::id();
        
        if(empty($page)){ $page=1; }
        
        //setting dynamic page size
        $page = ($page-1)*($pagesize);

        $q = "SELECT * FROM ocs_friendinvitation AS f JOIN ocs_person AS p ON f.fromuser = p.id WHERE f.touser=$id LIMIT $page,$pagesize";
        $r = EDatabase::q($q);
        
        $result = array();
        
        /*
        $i = 0;
        while($row=mysqli_fetch_assoc($r)){
            $result[$i]["personid"] = $row["login"];
            $result[$i]["message"] = $row["message"];
        }
        $i += 1;
        */
        
        while($row=mysqli_fetch_assoc($r)){
            $result[]["personid"] = $row["login"];
        }
        
        return $result;
    }
}

class OCSActivityLister extends OCSLister {
    
    //variables
    private $table;
    private $datatable;
    //global instance
    public $main;
    
    //inheriting constructor
    public function __construct($data="ocs_activity"){
        //setting initial value for table
        if(!empty($data)){
            $this->table = $data;
            $this->datatable = new EModel($this->table);
        } else {
            $this->set_table_search($this->table);
        }
    }
    
    public function ocs_activity_list($user,$page=1,$pagesize=10){
        
        if(empty($page)){ $page=1; }
        
        //setting dynamic page size
        $page = ($page-1)*($pagesize);
        
        $id = OCSUser::id();
        $q = "SELECT a.id, a.type, a.person, a.timestamp, a.message, p.login, p.firstname, p.lastname, p.email FROM ocs_activity AS a JOIN ocs_person AS p ON a.person=p.id  WHERE a.person IN (SELECT f.id2 FROM ocs_friendship AS f JOIN ocs_person AS p on (f.id1 = p.id) WHERE p.login='".OCSUser::login()."') LIMIT $page,$pagesize;";
        $r = EDatabase::q($q);
        
        $result = array();
        $i = 0;
        while($row=mysqli_fetch_assoc($r)){
            $result[$i]["id"] = $row["id"];
            $result[$i]["firstname"] = $row["firstname"];
            $result[$i]["lastname"] = $row["lastname"];
            $result[$i]["personid"] = $row["login"];
            $result[$i]["timestamp"] = $row["timestamp"];
            $result[$i]["type"] = $row["type"];
            $result[$i]["message"] = $row["message"];
            $i += 1;
        }
        
        return $result;
        
    }
}

?>
