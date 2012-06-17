<?php

/*
 * Suspended development due to refactoring.
 * Maybe not needed.
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

?>
