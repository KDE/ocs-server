<?php

/*
 *   GFX 4
 * 
 *   support:	happy.snizzo@gmail.com
 *   website:	http://trt-gfx.googlecode.com
 *   credits:	Claudio Desideri
 *   
 *   This software is released under the MIT License.
 *   http://opensource.org/licenses/mit-license.php
 */ 

/**
 * to_array() will convert the given XML text to an array in the XML structure
 */

class EXmlParser{
	
	public static function to_array($contents, $force_multiple=array()) {
		if(!$contents) return array();

		if(!function_exists('xml_parser_create')) {
			//print "'xml_parser_create()' function not found!";
			return array();
		}

		//Get the XML parser of PHP - PHP must have this module for the parser to work
		$parser = xml_parser_create('');
		xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parse_into_struct($parser, trim($contents), $xml_values);
		xml_parser_free($parser);
		
		if(!$xml_values) return;
		
		$data = EXmlParser::parse_array(array(),$xml_values,$force_multiple);
		
		return $data;
	}
	
	/**
	* Convert an xml tree to an associative array, supporting also duplicate keys
	*
	* @input	$data		give always an empty array
	* @input	&$tags		declarative array generated from xml_parse_into_struct()
	* @returns	$data		associative array
	*/
	public static function parse_array($data,&$tags,$force_multiple=array()){
		do{
			if(is_array($tags)){
				$entry = array_shift($tags);
			} else {
				return array();
			}
		
			if(isset($entry["tag"])){ $tag = $entry["tag"]; }
			if(isset($entry["type"])){ $type = $entry["type"]; }
			if(isset($entry["value"])){  $value = $entry["value"]; } else { $value = ''; }
		
			//just add value
			if($type=='complete') {
				$data[$tag] = $value;
			}
		
			//open new tag
			if($type=='open'){
				if(isset($data[$tag])){
					//if array is associative, it means it's not a collection of nodes with the same name
					if(EXmlParser::is_assoc($data[$tag])){
						$prev = $data[$tag];
						$new = EXmlParser::parse_array(array(),$tags,$force_multiple);
						$data[$tag] = array();
						$data[$tag][] = $prev;
						$data[$tag][] = $new;
					} else { //it's already a collection
						$new = EXmlParser::parse_array(array(),$tags,$force_multiple);
						$data[$tag][] = $new;
					}
					
				} else {
					if (is_array($force_multiple) and in_array($tag, $force_multiple)) {
						$data[$tag] = array();
						$data[$tag][] = EXmlParser::parse_array($data[$tag],$tags,$force_multiple);
					} else {
						$data[$tag] = array();
						$data[$tag] = EXmlParser::parse_array($data[$tag],$tags,$force_multiple);
					}
				}
			}
		
			//close tags
			if($type=='close') {
				return $data;
			}
		
		}while(count($tags)>0);
	
		return $data;
	}
	
	/*
	* Check if the given array is associative (key => value) or declarative (value, value, value)
	*/
	public static function is_assoc($array) {
		return (bool)count(array_filter(array_keys($array), 'is_string'));
	}
	
}
