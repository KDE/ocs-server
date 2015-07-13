<?php

/*
 *   GFX 4
 * 
 *   support:	happy.snizzo@gmail.com
 *   website:	http://www.gfx3.org
 *   credits:	Claudio Desideri
 *   
 *   This software is released under the MIT License.
 *   http://opensource.org/licenses/mit-license.php
 */ 

/* Use this class to protect your webpage.
 * this works by adding those properties to local generic.conf.php:
 * 
 * enabled|yes
 * enabled|no
 * enabled|protected
 * 
 * which can be 'yes' or 'no'. If nonsense is written, gfx will keep no
 * as default.
 * 
 * password|yourpassword
 */

class EProtect {
	
	private static $localkey;
	
	public static function load(){
		EProtect::$localkey = ELoader::$subsite_path.'pwd';
		
		//ELoader::$subsite_path; cointains the actual caller of protect call
		//keep enabled as standard choice
		if(isset(EConfig::$data['generic']['enabled'])){
			//case in which it is 'no' or anything different from 'yes' or 'protected'
			if(EConfig::$data['generic']['enabled']!='yes' and EConfig::$data['generic']['enabled']!='protected'){
				die('Access denied.');
			}
			
			//asks for password
			if(EConfig::$data['generic']['enabled']=='protected'){
				if(!EProtect::checklogin()){
					echo '<html>';
					die(EProtect::loginform());
					echo '</html>';
				}
			}
		}
	}
	
	public static function logout(){
		EHeaderDataParser::del_cookie(EProtect::$localkey);
		$sp = ELoader::$subsite_path;
		header('Location: '.$sp.'');
	}
	
	/*
	 * checks login on a subsite using a dedicated cookie
	 */
	public static function checklogin(){
		$password = EHeaderDataParser::get_cookie(EProtect::$localkey);
		if($password){
			if($password == EConfig::$data['generic']['password']){
				return true;
			}
		}
		
		if(isset($_POST['password'])){
			$password = $_POST['password'];
			if($password == EConfig::$data['generic']['password']){
				EHeaderDataParser::set_cookie(EProtect::$localkey, $password, 60); //60seconds*30 = max 30 minutes session
				return true;
			}
		}
		
		return false;
	}
	/*
	 * prints a very basic login form, that lets you login with cookies
	 * to your protected website
	 */
	public static function loginform(){
		$thispage = ELoader::$request_uri;
		$html = '
		<div align="center">
			<form method="POST" action="'.$thispage.'">
				Password: <input type="password" name="password" size="15" /><br />
		  
				<p><input type="submit" value="Login" /></p>
			</form>
		</div>
		';
		
		return $html;
	}
}

?>
