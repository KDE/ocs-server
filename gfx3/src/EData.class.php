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

class EDataSetter{
	
	private $table_list = array();
	
	public function __construct(){
		//
	}
	
	public function table_list(){
		$tlbs = new ECache("tables_list");
		if(!$tbls->exists()){
			$q = $edb->q('show tables');
			while($row=mysql_fetch_array($q)){
				
			}
		}
	}
	
}

class EData {
	//VARIABILI
	//variabile cache per evitare queries inutili
	private $dbg = false;
	private $tcount = "nd";
	private $table = false;
	private $fields = array();
	private $noquery = false;
	
	public function __construct($tbl){
		$this->table = $tbl;
		$this->tableInfo();
		$this->dbg = false;
	}
	
	
	
	//stampa informazioni sulle variabili interne della classe
	//da non usare in ambiente di produzione
	public function debugInfo(){
		echo "tcount: ".$this->tcount."<br>";
		echo "table: ".$this->table."<br>";
		echo "fields: <br><pre>";
		var_dump($this->fields); 
		echo "</pre>";
	}
	
	public function setNoQuery(){
		$this->noquery = true;
	}
	
	//methods
	private function tableInfo(){
		//if already cached load from cache else load with a describe AND cache
		if(file_exists("gfx3/cache/".$this->table.".table.txt")){
			$cache = file("gfx3/cache/".$this->table.".table.txt");
			for($i=0; $i<count($cache); $i++){
				$pattern = explode("|", $cache[$i]);
				$this->fields[$i]["field"] = $pattern[0];
				$this->fields[$i]["type"] = rtrim($pattern[1], "\n");
			}
		} else {
			$tbldata = $edb->q("DESCRIBE ".$this->table);
			$fieldsIndex = 0;
			$stream = fopen("gfx3/cache/".$this->table.".table.txt",'a+');
			while($row=mysql_fetch_array($tbldata)){
				$type = "null";
				if(stristr($row['Type'], "varchar")){ $type = "varchar"; }
				if(stristr($row['Type'], "int")){ $type = "int"; }
				if(stristr($row['Type'], "text")){ $type = "text"; }
				
				$this->fields[$fieldsIndex]["field"] = $row['Field'];
				$this->fields[$fieldsIndex]["type"] = $type;
				$fieldsIndex += 1;
				
				fwrite($stream, $row['Field']."|".$type."\n");
				
			}
			fclose($stream);
		}
	}
	
	public function fields(){
		return $this->fields;
	}
	
	public function insert($entries=array()) {
		if(empty($entries)){
			$index = 0;
			foreach($this->fields as $field){
				if(isset($_REQUEST[$field['field']])){
					$entries[$index] = array("field" => $field['field'], "value" => mysql_real_escape_string($_REQUEST[$field['field']]), "type" => $field['type']);
				}
				$index += 1;
			}
		}
		if(!empty($entries)){
			$sql = "INSERT INTO ".$this->table." (";
			foreach($entries as $entry){ $sql = $sql.$entry['field'].","; }
			//fix this, delete comma at the end of $sql, complete query writing go on on other
			$sql = rtrim($sql,",").") VALUES (";
			foreach($entries as $entry){ 
				if($field['type']=="varchar" or $field['type']=="text"){
					$sql = $sql."'".$entry['value']."',";
				} else if($field['type']=="int") {
					if(preg_match("/[^0-9]/", $entry['value'])){
						//data type error
						echo "<span style=\"font-family:Arial,sans-serif\">Warning! GFX3 <span style=\"color:red\">EData Object Error</span>: wrong data passed for <i><big>`".$field['field']."`</big></i> with type `INT`! freezing...</span><br>";
						die();
					}
					$sql = $sql.$entry['value'].",";
				}
			}
			$sql = rtrim($sql,",").")";
			if($this->noquery==false){
				$edb->q($sql);
			} else {
				echo $sql;
			}
		}
	}
	
	public function find($what=" * ", $where="") {
		if($this->dbg==true){
			echo "EXECUTING: SELECT $what FROM ".$this->table." $where <br>";
		}
		$r = $edb->q("SELECT $what FROM ".$this->table." $where");
		while($row=mysql_fetch_array($r)){
			$result[] = $row;
		}
		if(isset($result)){
			return $result;
		} else {
			return false;
		}
		
	}
	
	public function take($what=" * ", $where="") {
		if(!empty($where)){ $where = " WHERE ".$where." "; }
		
		$result = $edb->sq("SELECT $what FROM ".$this->table." $where");
		
		return $result;
		
	}
	
	public function row($what=" * ", $where="") {
		if(!empty($where)){ $where = " ".$where." "; }
		
		$r = $edb->q("SELECT $what FROM ".$this->table." $where");
		
		while($row=mysql_fetch_array($r)){
			$result = $row;
		}
		
		return $result;
		
	}
	
	public function count($field="id", $where=""){
		//optimized... only one query in a page!
		if($this->tcount!="nd"){
			return $this->tcount;
		}
		
		if(!empty($where)){ $where = " WHERE ".$where." "; }
		
		$r = $edb->q("SELECT COUNT($field) FROM ".$this->table." $where");
		while($row=mysql_fetch_array($r)){
			$result = $row[0];
		}
		
		$this->tcount = $result;
		return $result;
	}
	
	public function delete($where="", $howmany=""){
		if(!empty($where)){ $where = " WHERE ".$where." "; }
		if(!empty($howmany)){ $howmany = " LIMIT ".$howmany." "; }
		
		$edb->q("DELETE FROM ".$this->table." $where $howmany");
	}
	
	public function update($where="", $entries=array()) {
		//recupero le informazioni di where
		if(!empty($where)){ $where = " WHERE ".$where." "; }
		
		//recupero le informazioni automaticamente
		if(empty($entries)){
			$index = 0;
			foreach($this->fields as $field){
				//skippo id perchè non si modificano mai
				if($field['field']!="id"){
					//se è settato via post o get un valore con il nome del campo
					if(isset($_REQUEST[$field['field']])){
						$entries[$index] = array("field" => $field['field'], "value" => mysql_real_escape_string($_REQUEST[$field['field']]), "type" => $field['type']);
					}
					$index += 1;
				} else {
					if($this->dbg==true){
						echo "UPDATE di SKIPPING...<br>";
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
			$edb->q($sql);
		}
	}
	
}

?>
