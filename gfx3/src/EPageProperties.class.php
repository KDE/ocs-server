<?php

/*
 *   TRT GFX 3.0.1 (beta build) BackToSlash
 * 
 *   support:	happy.snizzo@gmail.com
 *   website:	http://www.gfx3.org
 *   credits:	Claudio Desideri
 *   
 *   This software is released under the MIT License.
 *   http://opensource.org/licenses/mit-license.php
 */ 


/*
 * Static class used as a helper for managing various web page properties.
 */
class EPageProperties {
	
	public static $request_uri = '';
	
	public static function get_page_name(){
		
		$name = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
		$name = substr($name,strrpos($name,"?"));
		$name = substr($name,strrpos($name,"/"));
		
		return $name;
	}
	
	/*
	 * Returns the exact website in which is running this system.
	 * For example: http://www.gfx3.org
	 */
	public static function get_current_website_url(){
		$pageURL = 'http';
		//TODO: implements https?
		$pageURL .= "://";
		$pageStripped = explode("/", $_SERVER["REQUEST_URI"]);
		$pageStripped = $pageStripped[0];
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"].$pageStripped;
		}
		
		return $pageURL;
	}
	
	public static function get_previous_page(){
		$prevpage = $_SERVER['HTTP_REFERER'];
		$prevpage = EHeaderDataParser::erase_get_data($prevpage);
		return $prevpage;
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
	 * 
	 * can be faulty...
	 */
	public static function get_clear_domain($domain)
	{
		$chunks = explode('.',$domain);
		if(isset($chunks[0])){
			if($chunks[0]=='www' and isset($chunks[1])){
				return $chunks[1];
			} else {
				return $chunks[0];
			}
		}
	}
	
}

?>
