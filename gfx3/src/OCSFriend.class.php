<?php

/*
 *   GFX 4
 *   
 *   support:   happy.snizzo@gmail.com
 *   website:   https://projects.kde.org/projects/playground/www/ocs-server
 *   credits:   Claudio Desideri
 *   
 *   This software is released under the MIT License.
 *   http://opensource.org/licenses/mit-license.php
 */

class OCSFriend{
    
    /*
     * Enabling main to be on a global context.
     */
    public function __construct(){
        $this->ocs_friendinvitation = new EData("ocs_friendinvitation");
        $this->ocs_friendship = new EData("ocs_friendship");
    }
    
    //   /v1/friend/invite/pid
    public static function send_invitation($touser, $message){
        $idfrom = OCSUser::id();
        
        $info = OCSUser::get_user_info($touser);
        $id = $info[0]["id"];
        
        $ocs_friendinvitation = new EData("ocs_friendinvitation");
        if(!$ocs_friendinvitation->is_there("touser","(fromuser=$idfrom and touser=$id) or (touser=$idfrom and fromuser=$id)")){
            EDatabase::q("INSERT INTO ocs_friendinvitation (fromuser,touser,message) VALUES ($idfrom,$id,'$message')");
        }
    }
    
    //   /v1/friend/approve/pid
    public static function approve_invitation($touser){
        $idfrom = OCSUser::id();
        $datafrom = OCSUser::get_user_info($idfrom);
        $loginfrom = $datafrom[0]["login"];
        
        
        $info = OCSUser::get_user_info($touser);
        $id = $info[0]["id"];
        
        //creating new table object
        $ocs_friendinvitation = new EData("ocs_friendinvitation");
        
        if($ocs_friendinvitation->is_there("touser","(fromuser=$idfrom and touser=$id) or (touser=$idfrom and fromuser=$id)"))
        {
            EDatabase::q("DELETE FROM ocs_friendinvitation WHERE (fromuser=$id AND touser=$idfrom) OR (touser=$id AND fromuser=$idfrom) LIMIT 2");
            EDatabase::q("INSERT INTO ocs_friendship (id1,id2) VALUES ($idfrom,$id)");
            EDatabase::q("INSERT INTO ocs_friendship (id1,id2) VALUES ($id,$idfrom)");
            
            //adding activity messages
            OCSActivity::add($idfrom, 2, OCSUser::login()." became friend with $touser.");
            OCSActivity::add($id, 2, "$touser became friend with ".OCSUser::login().".");
        }
    }
    
    //   /v1/friend/decline/pid
    public static function decline_invitation($touser){
        $idfrom = OCSUser::id();
        
        $info = OCSUser::get_user_info($touser);
        $id = $info[0]["id"];
        
        //creating new table object
        $ocs_friendinvitation = new EData("ocs_friendinvitation");
        
        EDatabase::q("DELETE FROM ocs_friendinvitation WHERE (fromuser=$id AND touser=$idfrom) OR (touser=$id AND fromuser=$idfrom) LIMIT 1");
    }
    
        //   /v1/friend/cancel/pid
    public static function cancel_friendship($touser){
        $idfrom = OCSUser::id();
        
        $info = OCSUser::get_user_info($touser);
        $id = $info[0]["id"];
        
        //creating new table object
        $ocs_friendinvitation = new EData("ocs_friendship");
        
        EDatabase::q("DELETE FROM ocs_friendship WHERE (id1=$idfrom AND id2=$id) OR (id2=$idfrom AND id1=$id) LIMIT 2");
    }
    
}


?>
