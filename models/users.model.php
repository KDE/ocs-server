<?php

class UsersModel extends EModel
{
        public function __construct()
        {
                parent::__construct("users");
        }
        
        /*
         * Performs a standard registration to the service.
         * This method doesn't provide a good vcode in order to activate the account
         */
        public function register()
        {
            $mail = EHeaderDataParser::db_get("mail");
            $nick = EHeaderDataParser::db_get("nick");
            
            $res = $this->find("verified", "where mail=\"" . $mail ."\" LIMIT 1");
            
            if(isset($res[0])){
                    if($res[0]["verified"]=="yes"){
                            return 101;
                            
                    }
            }
            
            $res = $this->find("verified", "where nick=\"" . $nick ."\" LIMIT 1");
            if(isset($res[0])){
                    if($res[0]["verified"]=="yes"){
                            return 102;
                            
                    }
            }
            
            $this->delete("mail=\"" . $mail ."\"");
            $this->insert(array("nick", "pass", "mail", "verified"));
            return 100;
        }
        
        /*
         * Just a method that authenticate the client for a request
         */
        public function auth($nick,$pass)
        {
                $res = $this->find("verified", "where (nick=\"" . $nick ."\" or mail=\"" . $nick ."\")  and pass=\"" . $pass ."\" LIMIT 1");
                
                if(isset($res[0])){
                        if($res[0]["verified"]=="yes"){
                                return true;
                        }
                }
                return false;
        }
		
        /*
        * Auth unverified only works with unverified accounts.
        * If a user is found, then email address is returned,
        * else false.
        */
        public function auth_unverified($nick,$pass)
        {
                $res = $this->find("*", "where nick=\"" . $nick ."\" and pass=\"" . $pass ."\" LIMIT 1");
                
                if(isset($res[0])){
                        if($res[0]["verified"]=="no"){
                                return $res[0]["mail"];
                        }
                }
                return false;
        }
        
        public function set_vcode($nick, $pass, $mail)
        {
                if($this->auth_unverified($nick,$pass)==$mail){
                        $hash = md5($nick.$pass.time());
                        $q = "UPDATE users SET vcode='".$hash."' WHERE mail=\"" . $mail ."\" LIMIT 1";
                        $r = EDatabase::q($q);
                        
                        return $hash;
                } else {
                        return false;
                }
        }
        
        public function from_hash_to_mail($hash)
        {
                $res = $this->find("mail, verified", "where vcode=\"" . $hash ."\" LIMIT 1");
                
                if(isset($res[0])){
                        if($res[0]["verified"]=="no"){
                                return $res[0]["mail"];
                        }
                }
                return false;
        }
        
        public function verify($mail)
        {
                $q = "UPDATE users SET verified='yes' WHERE mail=\"" . $mail ."\" LIMIT 1";
                $r = EDatabase::q($q);
        }
}

?>
