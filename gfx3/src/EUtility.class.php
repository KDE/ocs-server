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

class EUtility {
	
	//actually this function is needed by some files, but I don't actually know why.
	//and what this function did. Put this for now, but can generate errors.
	public static function stripslashes($string) {
		return str_replace('\\','',$string);
	}
	
	public static function br2nl($string) { 
		return str_replace('<br>', '\r\n', $string);
	}
	
	public static function nl2br($string) {
		$string = str_replace('\r\n', '<br>', $string);
		$string = str_replace('\r', '<br>', $string);
		$string = str_replace('\n', '<br>', $string);
		return $string;
	}
	
	/*Use this function to protect your webpage.
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
	* 
	* which will be your password that you have to pass with ?password=yourpassword
	* in your get requests.
	* 
	* TODO: move this to EProtect
	*/
	public static function protect()
	{
		//keep enabled as standard choice
		if(isset(EConfig::$data['generic']['enabled'])){
			//case in which it is 'no' or anything different from 'yes' or 'protected'
			if(EConfig::$data['generic']['enabled']!='yes' and EConfig::$data['generic']['enabled']!='protected'){
				die('Access denied.');
			}
			
			//asks for password
			if(EConfig::$data['generic']['enabled']=='protected'){
				//asks for password
			}
		}
	}
	
	public static function redirect($page)
	{
		header('location: $page');
	}
	
	/*
	 * TODO DOC
	 */
	public static function hide_output()
	{
		ob_start();
	}
	
	public static function show_output()
	{
		return ob_get_clean();
	}
	
	/**
	 * @param string $domain Pass $_SERVER['SERVER_NAME'] here
	 * @param bool $debug
	 *
	 * @debug bool $debug
	 * @return string
	 */
	public static function get_domain($domain, $debug = false)
	{
		$original = $domain = strtolower($domain);

		if (filter_var($domain, FILTER_VALIDATE_IP)) { return $domain; }

		$debug ? print('<strong style="color:green">&raquo;</strong> Parsing: '.$original) : false;

		$arr = array_slice(array_filter(explode('.', $domain, 4), function($value){
			return $value !== 'www';
		}), 0); //rebuild array indexes

		if (count($arr) > 2)
		{
			$count = count($arr);
			$_sub = explode('.', $count === 4 ? $arr[3] : $arr[2]);

			$debug ? print(" (parts count: {$count})") : false;

			if (count($_sub) === 2) // two level TLD
			{
				$removed = array_shift($arr);
				if ($count === 4) // got a subdomain acting as a domain
				{
					$removed = array_shift($arr);
				}
				$debug ? print("<br>\n" . '[*] Two level TLD: <strong>' . join('.', $_sub) . '</strong> ') : false;
			}
			elseif (count($_sub) === 1) // one level TLD
			{
				$removed = array_shift($arr); //remove the subdomain

				if (strlen($_sub[0]) === 2 && $count === 3) // TLD domain must be 2 letters
				{
					array_unshift($arr, $removed);
				}
				else
				{
					// non country TLD according to IANA
					$tlds = array(
						'aero',
						'arpa',
						'asia',
						'biz',
						'cat',
						'com',
						'coop',
						'edu',
						'gov',
						'info',
						'jobs',
						'mil',
						'mobi',
						'museum',
						'name',
						'net',
						'org',
						'post',
						'pro',
						'tel',
						'travel',
						'xxx',
					);

					if (count($arr) > 2 && in_array($_sub[0], $tlds) !== false) //special TLD don't have a country
					{
						array_shift($arr);
					}
				}
				$debug ? print("<br>\n" .'[*] One level TLD: <strong>'.join('.', $_sub).'</strong> ') : false;
			}
			else // more than 3 levels, something is wrong
			{
				for ($i = count($_sub); $i > 1; $i--)
				{
					$removed = array_shift($arr);
				}
				$debug ? print("<br>\n" . '[*] Three level TLD: <strong>' . join('.', $_sub) . '</strong> ') : false;
			}
		}
		elseif (count($arr) === 2)
		{
			$arr0 = array_shift($arr);

			if (strpos(join('.', $arr), '.') === false
				&& in_array($arr[0], array('localhost','test','invalid')) === false) // not a reserved domain
			{
				$debug ? print("<br>\n" .'Seems invalid domain: <strong>'.join('.', $arr).'</strong> re-adding: <strong>'.$arr0.'</strong> ') : false;
				// seems invalid domain, restore it
				array_unshift($arr, $arr0);
			}
		}

		$debug ? print("<br>\n".'<strong style="color:gray">&laquo;</strong> Done parsing: <span style="color:red">' . $original . '</span> as <span style="color:blue">'. join('.', $arr) ."</span><br>\n") : false;

		return join('.', $arr);
	}
	
	/**
	 * Get domain without .com or .co.uk etc...
	 * "www.example.com" -> "example"
	 */
	public static function get_clear_domain($domain)
	{
		$chunks = explode('.',$domain);
		if(isset($chunks[0])){
			return $chunks[0];
		}
	}

	
}

?>
