<?php
/*
 * on this file gfx inclusion is useless as gfx is already running
 */

class StepsController extends EController
{
	public function index()
	{
		//empty for now
	}
	
	public function _error($s)
	{
		echo '<span style="color:red">'.$s.'</span>';
	}
	
	public function _notify($s)
	{
		echo '<span style="color:green">'.$s.'</span>';
	}
	
	public function step1($args)
	{
		EStructure::view("header");
        
        echo "<h3>Step 1: configuring your database:</h3>";
        echo "Please define your database configuration:<br><br>";
        
        $name = EHeaderDataParser::post('name');
        $host = EHeaderDataParser::post('host');
        $user = EHeaderDataParser::post('user');
        $pass = EHeaderDataParser::post('password');
        $pass2 = EHeaderDataParser::post('password2');
        
        if(!empty($name) and !empty($user) and !empty($host) and !empty($pass) and !empty($pass2)){
			if($pass!=$pass2){
				$this->_error('Warning! Your passwords didn\'t match! Please reinsert them!');
			} else {
				$categories_path = ELoader::$prev_path.'/config/database.conf.php';
				
				$cf = new EConfigFile();
				$cf->set_abs_file($categories_path);
				
				$cf->set('name', $name);
				$cf->set('user', $user);
				$cf->set('host', $host);
				$cf->set('password', $pass);
				
				$cf->save();
				
				EDatabase::set_db_info($name,$host,$user,$pass);
				if(!EDatabase::open_session()){
					$this->_error('Couldn\'t open connection to database! Please check config!');
				} else {
					$this->_notify('We can connect to database!');
				}
			}
		}
		
        echo '
        <form action="/wizard/steps/step1" method="post">
        <input type="text" name="name" placeholder="database name"><br>
        <input type="text" name="host" placeholder="host"><br>
        <input type="text" name="user" placeholder="user"><br>
        <input type="password" name="password" placeholder="password"><br>
        <input type="password" name="password2" placeholder="repeat password"><br>
        
        <input type="submit" value="prova"><br>
        </form>
        ';
        EStructure::view("footer");
	}
}

?>
