<?php

class OCSActivity {

    //variables
    private $table;
    private $datatable;
    //global instance
    public $main;

    public static function add($user,$type, $message){
        //$timestamp = date('c');
        $timestamp = time();
        
        $q = "INSERT INTO ocs_activity (type,person,timestamp,message) VALUES ($type, $user, $timestamp, \"$message\")";
        $r = EDatabase::q($q);
    }
}

?>
