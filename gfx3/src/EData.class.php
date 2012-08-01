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

/*
 * fields()
 * insert()
 * find()
 * count()
 * delete()
 * update()
 */

class EData {
	//in order to avoid avoidable queries
	private $dbg = false;
	private $tcount = "nd";
	private $table = false;
	private $fields = array();
	private $noquery = false;
	
	public function __construct($tbl){
		$this->table = $tbl;
		if(EDatabase::table_exists($this->table)){
			$this->get_table_info();
		} else {
			ELog::error("$tbl does not exists on database.");
		}
		
		$this->dbg = false;
	}
	
	
	
	/*
	 * Prints debug informations about a database table.
	 */
	public function print_debug_info(){
		echo "tcount: ".$this->tcount."<br>";
		echo "table: ".$this->table."<br>";
		echo "fields: <br><pre>";
		var_dump($this->fields); 
		echo "</pre>";
	}
	
	/*
	 * Set this to true in order to make EData just simulate modifications
	 * and echo the query. Use only for debug purposes.
	 */
	public function set_simulate($b){
		$this->noquery = $b;
	}
	
	/*
	 * Rewrite debug rule.
	 */
	public function set_debug($b){
		$this->dbg = $b;
	}
	
	/*
	 * Load info from a database table.
	 * Results are also cached.
	 * TODO: move cache to new ECacheVar.
	 */
	private function get_table_info(){
		//if already cached load from cache else load with a describe AND cache
		$cache_name = $this->table.".table";
		if(ECacheVar::exists($cache_name)){
			$cache = new ECacheVar($cache_name);
			$data = $cache->get_array_assoc(); 
			foreach($cache as $key => $value){
				$fields[] = array("field" => $key,"type" => $value);
			}
			
		} else {
			$cache = new ECacheVar($cache_name);
			
			$describe = EDatabase::q("DESCRIBE ".$this->table);
			
			while($row=mysql_fetch_array($describe)){
				$type = "null";
				if(stristr($row['Type'], "varchar")){ $type = "varchar"; }
				if(stristr($row['Type'], "int")){ $type = "int"; }
				if(stristr($row['Type'], "text")){ $type = "text"; }
				
				$this->fields[] = array("field" => $row['Field'],"type" => $type);
				
				$cache->set($row['Field'],$type);
			}
		}
	}
	
	/*
	 * Returns all fields contained in table structure.
	 */
	public function fields(){
		return $this->fields;
	}
	
	/*
	 * Perform an automatic insert using data passed through GET/POST.
	 * To use only if user has every access to the database table.
	 * 
	 * Note that is $allowed_arrays is empty, every field is considered valid.
	 * TODO: fix this retrocompatibility crap ^^^
	 */
	public function insert($allowed_fields=array()) {
		
		//accepting eventual data as valid
		if(!empty($allowed_fields)){
			foreach($this->fields as $field){
				if(EHeaderDataParser::exists_post($field['field']) and in_array($field['field'],$allowed_fields)){
					$entries[] = array("field" => $field['field'], "value" => EHeaderDataParser::db_post($field['field']), "type" => $field['type']);
				}
			}
		} else {
			foreach($this->fields as $field){
				if(EHeaderDataParser::exists_post($field['field']) ){
					$entries[] = array("field" => $field['field'], "value" => EHeaderDataParser::db_post($field['field']), "type" => $field['type']);
				}
			}
		}
		
		if(!empty($entries)){
			$sql = "INSERT INTO ".$this->table." ("; //starting query
			foreach($entries as $entry){ $sql = $sql.$entry['field'].","; } //insert in queries all the fields we're going to accept
			$sql = rtrim($sql,",").") VALUES (";
			foreach($entries as $entry){
				//type check against type field found with describe
				if($field['type']=="varchar" or $field['type']=="text"){
					$sql = $sql."'".$entry['value']."',";
				} else if($field['type']=="int") {
					if(preg_match("/[^0-9]/", $entry['value'])){
						ELog::error("EData Object Error: wrong data passed for <i><big>`".$field['field']."`</big></i> with type `INT`! freezing...");
					}
					$sql = $sql.$entry['value'].",";
				}
			}
			$sql = rtrim($sql,",").")"; // cleaning and ending query
			
			//outputting or executing
			if($this->noquery==false){
				EDatabase::q($sql);
			} else {
				echo $sql;
			}
		}
	}
	
	/*
	 * Extrapolates data and map it into an associative array
	 */
	public function find($what=" * ", $where="") {
		$q = "SELECT $what FROM ".$this->table." $where";
		$r = EDatabase::q($q);
		while($arr = mysql_fetch_assoc($r)){
			$result[] = $arr;
		}
		
		if($this->noquery==false){
			if(isset($result)){
				return $result;
			} else {
				return false;
			}
		} else {
			echo $q;
		}
	}
	
	/*
	 * Return result from a single query.
	 * Example: COUNT(....) returns 56. This method returns 56.
	 */
	public function take($what=" * ", $where="") {
		if(!empty($where)){ $where = " WHERE ".$where." "; }
		
		$result = EDatabase::sq("SELECT $what FROM ".$this->table." $where");
		
		return $result;
		
	}
	
	/*
	 * Return a single row from an associative array
	 */
	public function row($what=" * ", $where="") {
		if(!empty($where)){ $where = " ".$where." "; }
		
		$r = EDatabase::q("SELECT $what FROM ".$this->table." $where");
		
		while($row=mysql_fetch_array($r)){
			$result = $row;
		}
		
		return $result;
		
	}
	
	/*
	 * Performs counts on selected table.
	 * TODO: maybe a little refactor using db->sq()?
	 */
	public function count($field="id", $where=""){
		//optimized... only one query in a page!
		if($this->tcount!="nd"){
			return $this->tcount;
		}
		
		if(!empty($where)){ $where = " WHERE ".$where." "; }
		
		$r = EDatabase::q("SELECT COUNT($field) FROM ".$this->table." $where");
		while($row=mysql_fetch_array($r)){
			$result = $row[0];
		}
		
		$this->tcount = $result;
		return $result;
	}
	
	/*
	 * check if exists a $field in $this->table $where
	 */
	public function is_there($field="", $where=""){
		$result = $this->count($field, $where);
		if($result){
			return true;
		} else {
			return false;
		}
	}
	
	/*
	 * Deletion method.
	 */
	public function delete($where="", $howmany=""){
		if(!empty($where)){ $where = " WHERE ".$where." "; }
		if(!empty($howmany)){ $howmany = " LIMIT ".$howmany." "; }
		
		EDatabase::q("DELETE FROM ".$this->table." $where $howmany");
	}
	
	
	/*
	 * Automatic update method. Works basically like insert method.
	 * Remember to specifies $where when used!
	 */
	public function update($where="", $allowed_fields=array()) {
		//recupero le informazioni di where
		if(!empty($where)){ $where = " WHERE ".$where." "; }
		
		//recupero le informazioni automaticamente
		if(empty($allowed_fields)){
			foreach($this->fields as $field){
				if($field['field']!="id"){
					if(EHeaderDataParser::exists_post($field['field']) and in_array($field['field'],$allowed_fields)){
						$entries[] = array("field" => $field['field'], "value" => EHeaderDataParser::db_post($field['field']), "type" => $field['type']);
					}
				}
			}
		} else {
			foreach($this->fields as $field){
				if($field['field']!="id"){
					if(EHeaderDataParser::exists_post($field['field']) ){
						$entries[] = array("field" => $field['field'], "value" => EHeaderDataParser::db_post($field['field']), "type" => $field['type']);
					}
				}
			}
		}
		//costruisco la query ed eseguo se ho le informazioni in entries
		if(!empty($entries)){
			$sql = "UPDATE ".$this->table." SET ";
			foreach($entries as $entry){
				$sql = $sql.$entry['field']."=";
				if($entry['type']=="int"){
					if(!is_numeric($entry['value'])){
						//data type error
						echo "<span style=\"font-family:Arial,sans-serif\">Warning! GFX3 <span style=\"color:red\">EData Object Error</span>: wrong data passed for <i><big>`".$field['field']."`</big></i> with type `INT`! freezing...</span><br>";
						die();
					}
					$sql = $sql.$entry['value'].",";
				} else {
					$sql = $sql."'".$entry['value']."',";
				}
			}
			$sql = rtrim($sql,",")." $where";
			
			if($this->noquery==false){
				EDatabase::q($sql);
			} else {
				echo $sql;
			}
		}
	}
	
}

?>
