<?php

include_once("gfx3/lib.php");

/**
* OCS Lib
*
* @author Frank Karlitschek 
* @copyright 2010 Frank Karlitschek karlitschek@kde.org 
* 
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
* License as published by the Free Software Foundation; either 
* version 3 of the License, or any later version.
* 
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
* GNU AFFERO GENERAL PUBLIC LICENSE for more details.
*	
* You should have received a copy of the GNU Lesser General Public 
* License along with this library.	If not, see <http://www.gnu.org/licenses/>.
* 


Documentation:
This libary is an example implementation of the Open Collaboration Services Specification you find here:
http://www.freedesktop.org/wiki/Specifications/open-collaboration-services

This libary is using PHP 5.x and MySQL 5.x
The OCS Libary is just an example implementation you can use as a reference or inspiration. 
It will probalby not run on your server unmodified because your datasources are different. But you should 
get an impression how the REST interface works and how you can make your data available in an OCS compatible way

You need a database table to track the API traffic.
The table should look like this:

CREATE TABLE IF NOT EXISTS `apitraffic` (
	`ip` bigint(20) NOT NULL,
	`count` int(11) NOT NULL,
	PRIMARY KEY (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


You need a file names "v1" in the htdocs of your webserver to handle the API requests. It could look like this:

	require_once('some of your libaries');
	require_once('ocs/lib_ocs.php');
	$this->handle();

You have to force apache to parse this file even it it doesn´t end with .php

	<Files v1>
		 ForceType application/x-httpd-php
	</Files>


*/

/**
 * Class to handle open collaboration services API requests
 *
 */
class H01_OCS {
	
	/**
	* define some configuration variables
	**/
	public $whitelist;
	public $maxpersonsearchpage;
	public $maxrequests; // per 15min from one IP
	public $maxrequestsauthenticated;
	
	public $main;
	
	public function __construct(){
		$this->whitelist = array('127.0.0.2');
		$this->maxpersonsearchpage = 200;
		$this->maxrequests = 1000; // per 15min from one IP
		$this->maxrequestsauthenticated = 2000;
		
		//GFX
		$this->main = new EMain();
	}
	
	/**
	 * reads input date from get/post/cookies and converts the date to a special data-type
	 *
	 * @param variable $key
	 * @param variable-type $type Supported variable types are: raw, text, int, float, array
	 * @param priority $getpriority
	 * @param default	$default
	 * @return data
	 */
	public  function readdata($key,$type='raw',$getpriority=false,$default='') {
		if($getpriority) {
			if(isset($_GET[$key])) {
				$data=$_GET[$key];
			} elseif(isset($_POST[$key])) {
				$data=$_POST[$key];
			} else {
				if($default=='') {
					if(($type=='int') or ($type=='float')) $data=0; else $data='';
				} else {
					$data=$default;
				}
			}
		} else {
			if(isset($_POST[$key])) {
				$data=$_POST[$key];
			} elseif(isset($_GET[$key])) {
				$data=$_GET[$key];
			} elseif(isset($_COOKIE[$key])) {
				$data=$_COOKIE[$key];
			} else {
				if($default=='') {
					if(($type=='int') or ($type=='float')) $data=0; else $data='';
				} else {
					$data=$default;
				}
			}
		}

		if($type=='raw') return($data);
		elseif($type=='text') return(addslashes(strip_tags($data)));
		elseif($type=='int')	{ $data = (int) $data; return($data); }
		elseif($type=='float')	{ $data = (float) $data; return($data); }
		elseif($type=='array')	{ $data = $data; return($data); }
		else { H01_UTIL::exception('readdata: internal error:'.$type); return(false); }
	}


	/**
		main function to handle the REST request
	**/
	public  function handle() {

		// overwrite the 404 error page returncode
		header("HTTP/1.0 200 OK");


		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			 $method='get';
		}elseif($_SERVER['REQUEST_METHOD'] == 'PUT') {
			 $method='put';
			 parse_str(file_get_contents("php://input"),$put_vars);
		}elseif($_SERVER['REQUEST_METHOD'] == 'POST') {
			 $method='post';
		}else{
			echo('internal server error: method not supported');
			exit();
		}

		// preprocess url
		$url=$_SERVER['PHP_SELF'];
		if(substr($url,(strlen($url)-1))<>'/') $url.='/';
		$ex=explode('/',$url);

		// eventhandler
		if(count($ex)==2){
			H01_GUI::showtemplate('apidoc');


		// CONFIG
		// apiconfig - GET - CONFIG
		}elseif(($method=='get') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='config') and (count($ex)==4)){
			$format=$this->readdata('format','text');
			$this->apiconfig($format);


		// personsearch - GET - PERSON/DATA				parameter als url parameter
		}elseif(($method=='get') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='person') and (strtolower($ex[3])=='data') and (count($ex)==5)){
			$format=$this->readdata('format','text');
			$username=$this->readdata('name','text');
			$country=$this->readdata('country','text');
			$city=$this->readdata('city','text');
			$description=$this->readdata('description','text');
			$pc=$this->readdata('pc','text');
			$software=$this->readdata('software','text');
			$longitude=$this->readdata('longitude','float');
			$latitude=$this->readdata('latitude','float');
			$distance=$this->readdata('distance','float');

			$attributeapp=$this->readdata('attributeapp','text');
			$attributekey=$this->readdata('attributekey','text');
			$attributevalue=$this->readdata('attributevalue','text');

			$page=$this->readdata('page','int');
			$pagesize=$this->readdata('pagesize','int');
			if($pagesize<1 or $pagesize>100) $pagesize=10;
			$this->personsearch($format,$username,$country,$city,$description,$pc,$software,$longitude,$latitude,$distance,$attributeapp,$attributekey,$attributevalue,$page,$pagesize);

		// personget - GET - PERSON/DATA/frank		 
		}elseif(($method=='get') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='person') and	(strtolower($ex[3])=='data') and (count($ex)==6)){
			$format=$this->readdata('format','text');
			$username=addslashes($ex[4]);
			$this->personget($format,$username);
		
		// personaccountbalance - GET - PERSON/BALANCE		 
		}elseif(($method=='get') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='person') and	(strtolower($ex[3])=='balance') and (count($ex)==5)){
			$format=$this->readdata('format','text');
			$this->persongetbalance($format);

		// personget - GET - PERSON/SELF		 
		}elseif(($method=='get') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='person') and	(strtolower($ex[3])=='self') and (count($ex)==5)){
			$format=$this->readdata('format','text');
			$this->personget($format);

		// personedit - POST - PERSON/SELF		 
		}elseif(($method=='post') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='person') and	(strtolower($ex[3])=='self') and (count($ex)==5)){
			$format=$this->readdata('format','text');
			$longitude=$this->readdata('longitude','float');
			$latitude=$this->readdata('latitude','float');
			$country=$this->readdata('country','text');
			$city=$this->readdata('city','text');
			$this->personedit($format,$longitude,$latitude,$country,$city);

		// personcheck - POST - PERSON/CHECK		 
		}elseif(($method=='post') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='person') and	(strtolower($ex[3])=='check') and (count($ex)==5)){
			$format=$this->readdata('format','text');
			$login=$this->readdata('login','text');
			$passwd=$this->readdata('password','text');
			$this->personcheck($format,$login,$passwd);

		// personadd - POST - PERSON/ADD		 
		}elseif(($method=='post') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='person') and	(strtolower($ex[3])=='add') and (count($ex)==5)){
			$format=$this->readdata('format','text');
			$login=$this->readdata('login','text');
			$passwd=$this->readdata('password','text');
			$firstname=$this->readdata('firstname','text');
			$lastname=$this->readdata('lastname','text');
			$email=$this->readdata('email','text');
			$this->personadd($format,$login,$passwd,$firstname,$lastname,$email);

		// persongetea - GET - PERSON/ATTRIBUTES/frank/parley/key		 
		}elseif(($method=='get') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='person') and	(strtolower($ex[3])=='attributes') and (count($ex)==8)){
			$format=$this->readdata('format','text');
			$username= addslashes($ex[4]);
			$app= addslashes($ex[5]);
			$key= addslashes($ex[6]);
			$this->personattributeget($format,$username,$app,$key);

		// persongetea - GET - PERSON/ATTRIBUTES/frank/parley 
		}elseif(($method=='get') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='person') and	(strtolower($ex[3])=='attributes') and (count($ex)==7)){
			$format=$this->readdata('format','text');
			$username= addslashes($ex[4]);
			$app= addslashes($ex[5]);
			$key= '';
			$this->personattributeget($format,$username,$app,$key);

		// persongetea - GET - PERSON/ATTRIBUTES/frank
		}elseif(($method=='get') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='person') and	(strtolower($ex[3])=='attributes') and (count($ex)==6)){
			$format=$this->readdata('format','text');
			$username= addslashes($ex[4]);
			$app= '';
			$key= '';
			$this->personattributeget($format,$username,$app,$key);

		// persondeleteea - POST - PERSON/DELETEATTRIBUTE/app/key
		}elseif(($method=='post') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='person') and	(strtolower($ex[3])=='deleteattribute') and (count($ex)==7)){
			$format=$this->readdata('format','text');
			$app= addslashes($ex[4]);
			$key= addslashes($ex[5]);
			$this->personattributedelete($format,$app,$key);

		// personsetea - POST - PERSON/SETATTRIBUTE/app/key
		}elseif(($method=='post') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='person') and	(strtolower($ex[3])=='setattribute') and (count($ex)==7)){
			$format=$this->readdata('format','text');
			$app= addslashes($ex[4]);
			$key= addslashes($ex[5]);
			$value=$this->readdata('value','text');
			$this->personattributeset($format,$app,$key,$value);



		// FAN
		//fanget - GET - FAN/DATA/"contentid" - page,pagesize als url parameter, 
		}elseif(($method=='get') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='fan') and (strtolower($ex[3])=='data') and (count($ex)==6)){						 
			$format=$this->readdata('format','text');
			$content=addslashes($ex[4]);
			$page=$this->readdata('page','int');
			$pagesize=$this->readdata('pagesize','int');
			if($pagesize<1 or $pagesize>100) $pagesize=10;
			$this->fanget($format,$content,$page,$pagesize);

		//isfan - GET - FAN/STATUS/"contentid"	
		}elseif(($method=='get') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='fan') and (strtolower($ex[3])=='status') and (count($ex)==6)){
			$format=$this->readdata('format','text');
			$content=addslashes($ex[4]);
			$this->isfan($format,$content);
		
		//addfan - POST - FAN/ADD/"contentid"	
		}elseif(($method=='post') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='fan') and (strtolower($ex[3])=='add') and (count($ex)==6)){
			$format=$this->readdata('format','text');
			$content=addslashes($ex[4]);
			$this->addfan($format,$content);
		
		//removefan - POST - FAN/REMOVE/"contentid"	
		}elseif(($method=='post') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='fan') and (strtolower($ex[3])=='remove') and (count($ex)==6)){
			$format=$this->readdata('format','text');
			$content=addslashes($ex[4]);
			$this->removefan($format,$content);



		// FRIEND
		//friendget - GET - FRIEND/DATA/"personid" - page,pagesize als url parameter, 
		}elseif(($method=='get') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='friend') and (strtolower($ex[3])=='data') and (count($ex)==6)){						 
			$format=$this->readdata('format','text');
			$username=addslashes($ex[4]);
			$page=$this->readdata('page','int');
			$pagesize=$this->readdata('pagesize','int');
			if($pagesize<1 or $pagesize>100) $pagesize=10;
			$this->friendget($format,$username,$page,$pagesize);

		//friendinvite - POST - FRIEND/INVITE/"username"/	 message als url parameter	
		}elseif(($method=='post') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='friend') and (strtolower($ex[3])=='invite') and (count($ex)==6)){					 
			$format=$this->readdata('format','text');
			$username=addslashes($ex[4]);
			$message=$this->readdata('message','text');
			$this->friendinvite($format,$username,$message);

		//friendapprove - POST - FRIEND/APPROVE/"username"/		 
		}elseif(($method=='post') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='friend') and (strtolower($ex[3])=='approve') and (count($ex)==6)){					 
			$format=$this->readdata('format','text');
			$username=addslashes($ex[4]);
			$this->friendapprove($format,$username);

		//frienddecline - POST - FRIEND/DECLINE/"username"/		 
		}elseif(($method=='post') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='friend') and (strtolower($ex[3])=='decline') and (count($ex)==6)){					 
			$format=$this->readdata('format','text');
			$username=addslashes($ex[4]);
			$this->frienddecline($format,$username);
	
		//friendcancel - POST - FRIEND/CANCEL/"username"/		 
		}elseif(($method=='post') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='friend') and (strtolower($ex[3])=='cancel') and (count($ex)==6)){					 
			$format=$this->readdata('format','text');
			$username=addslashes($ex[4]);
			$this->friendcancel($format,$username);
 
		//friendcancelinvitation - POST - FRIEND/CANCEL/"username"/		 
		}elseif(($method=='post') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='friend') and (strtolower($ex[3])=='cancelinvitation') and (count($ex)==6)){					 
			$format=$this->readdata('format','text');
			$username=addslashes($ex[4]);
			$this->friendcancelinvitation($format,$username);

		//friendsentinvitations - GET - FRIEND/SENTINVITATIONS/		 
		}elseif(($method=='get') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='friend') and (strtolower($ex[3])=='sentinvitations') and (count($ex)==5)){					 
			$format=$this->readdata('format','text');
			$page=$this->readdata('page','int');
			$pagesize=$this->readdata('pagesize','int');
			if($pagesize<1 or $pagesize>100) $pagesize=10;
			$this->friendsentinvitations($format,$page,$pagesize);
	
		//friendreceivedinvitations - GET - FRIEND/RECEIVEDINVITATIONS/		 
		}elseif(($method=='get') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='friend') and (strtolower($ex[3])=='receivedinvitations') and (count($ex)==5)){					 
			$format=$this->readdata('format','text');
			$page=$this->readdata('page','int');
			$pagesize=$this->readdata('pagesize','int');
			if($pagesize<1 or $pagesize>100) $pagesize=10;
			$this->friendreceivedinvitations($format,$page,$pagesize);


		// MESSAGE
		//messagefolders	- GET - MESSAGE/		
		}elseif(($method=='get') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='message') and (count($ex)==4)){				 
			$format=$this->readdata('format','text');
			$this->messagefolders($format);

		//messagelist - GET - MESSAGE/"folderid"/	 page,pagesize als url parameter
		}elseif((($method=='get') and strtolower($ex[1])=='v1') and (strtolower($ex[2])=='message') and (count($ex)==5)){						
			$format=$this->readdata('format','text');
			$folder= (int) addslashes($ex[3]);
			$filter=$this->readdata('status','text');
			$page=$this->readdata('page','int');
			$pagesize=$this->readdata('pagesize','int');
			if($pagesize<1 or $pagesize>100) $pagesize=10;
			$this->messagelist($format,$folder,$page,$pagesize,$filter);

		// messagesend	- POST - MESSAGE/"folderid"
		}elseif(($method=='post') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='message') and (strtolower($ex[3])=='2') and (count($ex)==5)){					
			$format=$this->readdata('format','text');
			$touser=$this->readdata('to','text');
			$subject=$this->readdata('subject','text');
			$message=$this->readdata('message','text');
			$this->messagesend($format,$touser,$subject,$message);

		// messageget - GET - MESSAGE/"folderid"/"messageid"	 
		}elseif(($method=='get') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='message') and (count($ex)==6)){					
			$format=$this->readdata('format','text');
			$folder= (int) addslashes($ex[3]);
			$message= (int) addslashes($ex[4]);
			$this->messageget($format,$folder,$message);


		// ACTIVITY
		// activityget - GET ACTIVITY	 page,pagesize als urlparameter
		}elseif(($method=='get') and (strtolower($ex[1])=='v1')and (strtolower($ex[2])=='activity') and (count($ex)==4)){					 
			$format=$this->readdata('format','text');
			$page=$this->readdata('page','int');
			$pagesize=$this->readdata('pagesize','int');
			if($pagesize<1 or $pagesize>100) $pagesize=10;
			$this->activityget($format,$page,$pagesize);

		// activityput - POST ACTIVITY
		}elseif(($method=='post') and (strtolower($ex[1])=='v1')and (strtolower($ex[2])=='activity')	and (count($ex)==4)){						
			$format=$this->readdata('format','text');
			$message=$this->readdata('message','text');
			$this->activityput($format,$message);


		// CONTENT
		// contentcategories - GET - CONTENT/CATEGORIES
		}elseif(($method=='get') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='content') and (strtolower($ex[3])=='categories') and (count($ex)==5)){			
			$format=$this->readdata('format','text');
			$this->contentcategories($format);
		
		// contentlicense - GET - CONTENT/LICENSES
		}elseif(($method=='get') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='content') and (strtolower($ex[3])=='licenses') and (count($ex)==5)){			
			$format=$this->readdata('format','text');
			$this->contentlicenses($format);

		// contentdistributions - GET - CONTENT/DISTRIBUTIONS
		}elseif(($method=='get') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='content') and (strtolower($ex[3])=='distributions') and (count($ex)==5)){
			$format=$this->readdata('format','text');
			$this->contentdistributions($format);

		// contentdependencies - GET - CONTENT/DISTRIBUTIONS
		}elseif(($method=='get') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='content') and (strtolower($ex[3])=='dependencies') and (count($ex)==5)){
			$format=$this->readdata('format','text');
			$this->contentdependencies($format);

		// contenthomepage - GET - CONTENT/HOMPAGES
		}elseif(($method=='get') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='content') and (strtolower($ex[3])=='homepages') and (count($ex)==5)){
			$format=$this->readdata('format','text');
			$this->contenthomepages($format);


		// contentlist - GET - CONTENT/DATA - category,search,sort,page,pagesize
		}elseif((($method=='get') and strtolower($ex[1])=='v1') and (strtolower($ex[2])=='content') and (strtolower($ex[3])=='data') and (count($ex)==5)){						
			$format=$this->readdata('format','text');
			$contents=$this->readdata('categories','text');
			$searchstr=$this->readdata('search','text');
			$searchuser=$this->readdata('user','text');
			$external=$this->readdata('external','text');
			$distribution=$this->readdata('distribution','text');
			$license=$this->readdata('license','text');
			$sortmode=$this->readdata('sortmode','text');
			$page=$this->readdata('page','int');
			$pagesize=$this->readdata('pagesize','int');
			if($pagesize<1 or $pagesize>100) $pagesize=10;
			$this->contentlist($format,$contents,$searchstr,$searchuser,$external,$distribution,$license,$sortmode,$page,$pagesize);

		// contentget - GET - CONTENT/DATA/"id"
		}elseif(($method=='get') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='content') and (strtolower($ex[3])=='data') and (count($ex)==6)){						 
			$format=$this->readdata('format','text');
			$id= addslashes($ex[4]);
			$this->contentget($format,$id);

		// contentdownload - GET - CONTENT/DOWNLOAD/"id"/"item"
		}elseif(($method=='get') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='content') and (strtolower($ex[3])=='download') and (count($ex)==7)){						 
			$format=$this->readdata('format','text');
			$id= addslashes($ex[4]);
			$item= addslashes($ex[5]);
			$this->contentdownload($format,$id,$item);

		// getrecommendations - GET - CONTENT/RECOMMENDATIONS/"id"
		}elseif(($method=='get') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='content') and (strtolower($ex[3])=='recommendations') and (count($ex)==6)){
			$format=$this->readdata('format','text');
			$id= addslashes($ex[4]);
			$page=$this->readdata('page','int');
			$pagesize=$this->readdata('pagesize','int');
			$this->contentrecommendations($id,$format,$page,$pagesize);


		// contentvote - POST - CONTENT/VOTE/"id" - good/bad als url parameter 
		}elseif((($method=='post') and strtolower($ex[1])=='v1') and (strtolower($ex[2])=='content') and (strtolower($ex[3])=='vote') and (count($ex)==6)){						
			$format=$this->readdata('format','text');
			$id= addslashes($ex[4]);
			$vote=$this->readdata('vote','text');
			$this->contentvote($format,$id,$vote);

		// contentpreviewdelete - POST - CONTENT/DELETEPREVIEW/"contentid"/"previewid"	 
		}elseif((($method=='post') and strtolower($ex[1])=='v1') and (strtolower($ex[2])=='content') and (strtolower($ex[3])=='deletepreview') and (count($ex)==7)){						
			$format=$this->readdata('format','text');
			$contentid= addslashes($ex[4]);
			$previewid= addslashes($ex[5]);
			$this->contentpreviewdelete($format,$contentid,$previewid);

		// contentpreviewupload - POST - CONTENT/UPLOADPREVIEW/"contentid"/"previewid"	 
		}elseif((($method=='post') and strtolower($ex[1])=='v1') and (strtolower($ex[2])=='content') and (strtolower($ex[3])=='uploadpreview') and (count($ex)==7)){						
			$format=$this->readdata('format','text');
			$contentid= addslashes($ex[4]);
			$previewid= addslashes($ex[5]);
			$this->contentpreviewupload($format,$contentid,$previewid);

		// contentdownloaddelete - POST - CONTENT/DELETEDOWNLOAD/"contentid"	 
		}elseif((($method=='post') and strtolower($ex[1])=='v1') and (strtolower($ex[2])=='content') and (strtolower($ex[3])=='deletedownload') and (count($ex)==6)){
			$format=$this->readdata('format','text');
			$contentid= addslashes($ex[4]);
			$this->contentdownloaddelete($format,$contentid);

		// contentdownloadupload - POST - CONTENT/UPLOADDOWNLOAD/"contentid"	 
		}elseif((($method=='post') and strtolower($ex[1])=='v1') and (strtolower($ex[2])=='content') and (strtolower($ex[3])=='uploaddownload') and (count($ex)==6)){
			$format=$this->readdata('format','text');
			$contentid= addslashes($ex[4]);
			$this->contentdownloadupload($format,$contentid);

		// contentadd - POST - CONTENT/ADD
		}elseif((($method=='post') and strtolower($ex[1])=='v1') and (strtolower($ex[2])=='content') and (strtolower($ex[3])=='add') and (count($ex)==5)){						
			$format=$this->readdata('format','text');
			$this->contentadd($format);

		// contentedit - POST - CONTENT/EDIT/"contentid"	 
		}elseif((($method=='post') and strtolower($ex[1])=='v1') and (strtolower($ex[2])=='content') and (strtolower($ex[3])=='edit') and (count($ex)==6)){						
			$format=$this->readdata('format','text');
			$contentid= addslashes($ex[4]);
			$this->contentedit($format,$contentid);

		// contentdelete - POST - CONTENT/DELETE/"contentid"	 
		}elseif((($method=='post') and strtolower($ex[1])=='v1') and (strtolower($ex[2])=='content') and (strtolower($ex[3])=='delete') and (count($ex)==6)){						
			$format=$this->readdata('format','text');
			$contentid= addslashes($ex[4]);
			$this->contentdelete($format,$contentid);
		


		// KNOWLEDGEBASE

		// knowledgebaseget - GET - KNOWLEDGEBASE/DATA/"id"
		}elseif(($method=='get') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='knowledgebase') and (strtolower($ex[3])=='data') and (count($ex)==6)){
			$format=$this->readdata('format','text');
			$id= addslashes($ex[4]);
			$this->knowledgebaseget($format,$id);

		// knowledgebaselist - GET - KNOWLEDGEBASE/DATA - category,search,sort,page,pagesize
		}elseif((($method=='get') and strtolower($ex[1])=='v1') and (strtolower($ex[2])=='knowledgebase') and (strtolower($ex[3])=='data') and (count($ex)==5)){
			$format=$this->readdata('format','text');
			$contents=$this->readdata('content','text');
			$searchstr=$this->readdata('search','text');
			$sortmode=$this->readdata('sortmode','text');
			$page=$this->readdata('page','int');
			$pagesize=$this->readdata('pagesize','int');
			if($pagesize<1 or $pagesize>100) $pagesize=10;
			$this->knowledgebaselist($format,$contents,$searchstr,$sortmode,$page,$pagesize);


		// EVENT

		// eventget - GET - EVENT/DATA/"id"
		}elseif(($method=='get') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='event') and (strtolower($ex[3])=='data') and (count($ex)==6)){
			$format=$this->readdata('format','text');
			$id= addslashes($ex[4]);
			$this->eventget($format,$id);

		// eventlist - GET - EVENT/DATA - type,country,startat,search,sort,page,pagesize
		}elseif((($method=='get') and strtolower($ex[1])=='v1') and (strtolower($ex[2])=='event') and (strtolower($ex[3])=='data') and (count($ex)==5)){
			$format=$this->readdata('format','text');
			$type=$this->readdata('type','int');
			$country=$this->readdata('country','text');
			$startat=$this->readdata('startat','text');
			$searchstr=$this->readdata('search','text');
			$sortmode=$this->readdata('sortmode','text');
			$page=$this->readdata('page','int');
			$pagesize=$this->readdata('pagesize','int');
			if($pagesize<1 or $pagesize>100) $pagesize=10;
			$this->eventlist($format,$type,$country,$startat,$searchstr,$sortmode,$page,$pagesize);


		// eventadd - POST - EVENT/ADD
		}elseif((($method=='post') and strtolower($ex[1])=='v1') and (strtolower($ex[2])=='event') and (strtolower($ex[3])=='add') and (count($ex)==5)){
			$format=$this->readdata('format','text');
			$this->eventadd($format);

		// eventedit - POST - EVENT/EDIT/"eventid"	 
		}elseif((($method=='post') and strtolower($ex[1])=='v1') and (strtolower($ex[2])=='event') and (strtolower($ex[3])=='edit') and (count($ex)==6)){
			$format=$this->readdata('format','text');
			$eventid= addslashes($ex[4]);
			$this->eventedit($format,$eventid);

		// eventdelete - POST - EVENT/DELETE/"eventid"	 
		}elseif((($method=='post') and strtolower($ex[1])=='v1') and (strtolower($ex[2])=='event') and (strtolower($ex[3])=='delete') and (count($ex)==6)){
			$format=$this->readdata('format','text');
			$eventid= addslashes($ex[4]);
			$this->eventdelete($format,$eventid);


		// COMMENTS

		// commentsget - GET - COMMENTS/GET
		}elseif((($method=='get') and strtolower($ex[1])=='v1') and (strtolower($ex[2])=='comments') and (strtolower($ex[3])=='data') and (count($ex)==8)){
			$type= addslashes($ex[4]);
			$content= addslashes($ex[5]);
			$content2= addslashes($ex[6]);
			$format=$this->readdata('format','text');
			$page=$this->readdata('page','int');
			$pagesize=$this->readdata('pagesize','int');
			if($pagesize<1 or $pagesize>2000) $pagesize=10;
			$this->commentsget($format,$type,$content,$content2,$page,$pagesize);

		// commentsadd - POST - COMMENTS/ADD	 
		}elseif((($method=='post') and strtolower($ex[1])=='v1') and (strtolower($ex[2])=='comments') and (strtolower($ex[3])=='add') and (count($ex)==5)){
			$format=$this->readdata('format','text');
			$type=$this->readdata('type','int');
			$content=$this->readdata('content','int');
			$content2=$this->readdata('content2','int');
			$parent=$this->readdata('parent','int');
			$subject=$this->readdata('subject','text');
			$message=$this->readdata('message','text');
			$this->commentsadd($format,$type,$content,$content2,$parent,$subject,$message);

		// commentvote - GET - COMMENTS/vote	 
		}elseif((($method=='post') and strtolower($ex[1])=='v1') and (strtolower($ex[2])=='comments') and (strtolower($ex[3])=='vote') and (count($ex)==6)){
			$id = addslashes($ex[4]);
			$score = $this->readdata('vote','int');
			$format=$this->readdata('format','text');
			$this->commentvote($format,$id,$score);


		// FORUM

		}elseif(strtolower($ex[1])=='v1' and strtolower($ex[2])=='forum'){
			$functioncall=strtolower($ex[3]);
			$subcall=strtolower($ex[4]);
			$argumentcount=count($ex);
			// list - GET - FORUM/LIST
			if($method=='get' and $functioncall=='list' and $argumentcount==4){
				$format=$this->readdata('format','text');
				$page=$this->readdata('page','int');
				$pagesize=$this->readdata('pagesize','int');
			// TOPIC section
			}elseif($functioncall=='topic'){
				// list - GET - FORUM/TOPIC/LIST
				if($method=='get' and $subcall=='list' and $argumentcount==10){
					$format=$this->readdata('format','text');
					$forum=$this->readdata('forum','int');
					$search=$this->readdata('search','text');
					$description=$this->readdata('description','text');
					$sortmode=$this->readdata('sortmode','text');
					$page=$this->readdata('page','int');
					$pagesize=$this->readdata('pagesize','int');
				// add - POST - FORUM/TOPIC/ADD
				}elseif($method=='post' and $subcall=='add' and $argumentcount==5){
					$format=$this->readdata('format','text');
					$subject=$this->readdata('subject','text');
					$content=$this->readdata('content','text');
					$forum=$this->readdata('forum','int');
				}
			}

		// BUILDSERVICE


		}elseif(strtolower($ex[1])=='v1' and strtolower($ex[2])=='buildservice' and count($ex)>4){
			$functioncall=strtolower($ex[4]);
			$argumentcount=count($ex);
			// PROJECT section
			if(strtolower($ex[3]=='project')){
				// create - POST - PROJECT/CREATE
				if($method=='post' and $functioncall=='create' and $argumentcount==6){
					$format=$this->readdata('format','text');
					$name=$this->readdata('name','text');
					$version=$this->readdata('version','text');
					$license=$this->readdata('license','text');
					$url=$this->readdata('url','text');
					$developers=$this->readdata('developers','text');
					$summary=$this->readdata('summary','text');
					$description=$this->readdata('description','text');
					$requirements=$this->readdata('requirements','text');
					$specfile=$this->readdata('specfile','text');
					
					$this->buildserviceprojectcreate($format,$name,$version,$license,$url,$developers,$summary,$description,$requirements,$specfile);
				// get - GET - PROJECT/GET/"project"
				}elseif($method=='get' and $functioncall=='get' and $argumentcount==7){
					$format=$this->readdata('format','text');
					$projectID=$ex[5];
					
					$this->buildserviceprojectget($format,$projectID);
				// delete - POST - PROJECT/DELETE/"project"
				}elseif($method=='post' and $functioncall=='delete' and $argumentcount==7){
					$format=$this->readdata('format','text');
					$projectID=$ex[5];
					
					$this->buildserviceprojectdelete($format,$projectID);
				// edit - POST - ROJECT/EDIT/"project"
				}elseif($method=='post' and $functioncall=='edit' and $argumentcount==7){
					$format=$this->readdata('format','text');
					$projectID=$ex[5];
					$name=$this->readdata('name','text');
					$version=$this->readdata('version','text');
					$license=$this->readdata('license','text');
					$url=$this->readdata('url','text');
					$developers=$this->readdata('developers','text');
					$summary=$this->readdata('summary','text');
					$description=$this->readdata('description','text');
					$requirements=$this->readdata('requirements','text');
					$specfile=$this->readdata('specfile','text');
					$this->buildserviceprojectedit($format,$projectID,$name,$version,$license,$url,$developers,$summary,$description,$requirements,$specfile);
				// listall - GET - PROJECT/LIST
				}elseif($method=='get' and $functioncall=='list' and $argumentcount==6){
					$format=$this->readdata('format','text');
					$page=$this->readdata('page','int');
					$pagesize=$this->readdata('pagesize','int');
					$this->buildserviceprojectlist($format,$page,$pagesize);
				// generatespecfile - GET - PROJECT/UPLOADSOURCE
				}elseif($method=='post' and $functioncall=='uploadsource' and $argumentcount==7){
					$format=$this->readdata('format','text');
					$projectID=$ex[5];
					$this->buildserviceprojectuploadsource($format,$projectID);
				}else{
					$this->reportapisyntaxerror('buildservice/project');
				}
			// REMOTEACCOUNTS section
			}elseif(strtolower($ex[3])=='remoteaccounts'){
				if($method=='get' and $functioncall=='list' and $argumentcount==6){
					$format=$this->readdata('format','text');
					$page=$this->readdata('page','int');
					$pagesize=$this->readdata('pagesize','int');
					$this->buildserviceremoteaccountslist($format,$page,$pagesize);
				}elseif($method=='post' and $functioncall=='add' and $argumentcount==6){
					$format=$this->readdata('format','text');
					$type=$this->readdata('type','int');
					$typeid=$this->readdata('typeid','text');
					$data=$this->readdata('data','text');
					$login=$this->readdata('login','text');
					$password=$this->readdata('password','text');
					$this->buildserviceremoteaccountsadd($format,$type,$typeid,$data,$login,$password);
				}elseif($method=='post' and $functioncall=='edit' and $argumentcount==7){
					$format=$this->readdata('format','text');
					$id=$ex[5];
					$data=$this->readdata('data','text');
					$login=$this->readdata('login','text');
					$password=$this->readdata('password','text');
					$this->buildserviceremoteaccountsedit($format,$id,$login,$password,$data);
				}elseif($method=='get' and $functioncall=='get' and $argumentcount==7){
					$format=$this->readdata('format','text');
					$id=$ex[5];
					$this->buildserviceremoteaccountsget($format,$id);
				}elseif($method=='post' and $functioncall=='remove' and $argumentcount==7){
					$format=$this->readdata('format','text');
					$id=$ex[5];
					$this->buildserviceremoteaccountsremove($format,$id);
				}else{
					$this->reportapisyntaxerror('buildservice/remoteaccounts');
				}
			// BUILDSERVICES section
			}elseif(strtolower($ex[3]=='buildservices')){
				if($method=='get' and $functioncall=='list' and $argumentcount==6){
					$format=$this->readdata('format','text');
					$page=$this->readdata('page','int');
					$pagesize=$this->readdata('pagesize','int');
					$this->buildservicebuildserviceslist($format,$page,$pagesize);
				}elseif($method=='get' and $functioncall=='get' and $argumentcount==7){
					$format=$this->readdata('format','text');
					$buildserviceID=$ex[5];
					$this->buildservicebuildservicesget($format,$buildserviceID);
				}else{
					$this->reportapisyntaxerror('buildservice/buildservices');
				}
			// JOBS section
			}elseif(strtolower($ex[3]=='jobs')){
				// getbuildcapabilities - GET - JOBS/GETBUILDCAPABILITIES
				if($method=='get' and $functioncall=='list' and $argumentcount==7){
					$format=$this->readdata('format','text');
					$projectID=$ex[5];
					$page=$this->readdata('page','int');
					$pagesize=$this->readdata('pagesize','int');
					$this->buildservicejobslist($format,$projectID,$page,$pagesize);
				// create - POST - JOBS/CREATE/"project"/"buildsevice"/"target"
				}elseif($method=='post' and $functioncall=='create' and $argumentcount==9){
					$format=$this->readdata('format','text');
					$projectID=$ex[5];
					$buildserviceID=$ex[6];
					$target=$ex[7];
					$this->buildservicejobscreate($format,$projectID,$buildserviceID,$target);
				// cancel - POST - JOBS/CANCEL/"buildjob"
				}elseif($method=='post' and $functioncall=='cancel' and $argumentcount==7){
					$format=$this->readdata('format','text');
					$buildjobID=$ex[5];
					$this->buildservicejobscancel($format,$buildjobID);
				// get - GET - JOBS/GET/"buildjob"
				}elseif($method=='get' and $functioncall=='get' and $argumentcount==7){
					$format=$this->readdata('format','text');
					$buildjobID=$ex[5];
					$this->buildservicejobsget($format,$buildjobID);
				// getoutput - GET - JOBS/GETOUTPOT/"buildjob"
				}elseif($method=='get' and $functioncall=='getoutput' and $argumentcount==7){
					$format=$this->readdata('format','text');
					$buildjobID=$ex[5];
					$this->buildservicejobsgetoutput($format,$buildjobID);
				}else{
					$this->reportapisyntaxerror('buildservice/jobs');
				}
			// PUBLISHING section
			}elseif(strtolower($ex[3]=='publishing')){
				// getpublishingcapabilities - GET - PUBLISHING/GETPUBLISHINGCAPABILITIES
				if($method=='get' and $functioncall=='getpublishingcapabilities' and $argumentcount==6){
					$format=$this->readdata('format','text');
					$page=$this->readdata('page','int');
					$pagesize=$this->readdata('pagesize','int');
					$this->buildservicepublishinggetpublishingcapabilities($format,$page,$pagesize);
				// getpublisher - GET - PUBLISHING/GETPUBLISHER
				}elseif($method=='get' and $functioncall=='getpublisher' and $argumentcount==7){
					$format=$this->readdata('format','text');
					$publisherID=$ex[5];
					$this->buildservicepublishinggetpublisher($format,$publisherID);
				// publishtargetresult - POST - PUBLISHING/PUBLISHTARGETRESULT/"buildjob"/"publisher"
				}elseif($method=='post' and $functioncall=='publishtargetresult' and $argumentcount==8){
					$format=$this->readdata('format','text');
					$buildjobID=$ex[5];
					$publisherID=$ex[6];
					$this->buildservicepublishingpublishtargetresult($format,$buildjobID,$publisherID);
				// savefields - POST - PUBLISHING/SAVEFIELDS/"project"
				}elseif($method=='post' and $functioncall=='savefields' and $argumentcount==7){
					$format=$this->readdata('format','text');
					$projectID=$ex[5];
					$fields=$this->readdata('fields','array');
					$this->buildservicepublishingsavefields($format,$projectID,$fields);
				// getfields - GET - PUBLISHING/GETFIELDS/"project"
				}elseif($method=='get' and $functioncall=='getfields' and $argumentcount==7){
					$format=$this->readdata('format','text');
					$projectID=$ex[5];
					$this->buildservicepublishinggetfields($format,$projectID);
				}else{
					$this->reportapisyntaxerror('buildservice/publishing');
				}
			}else{
				$this->reportapisyntaxerror('buildservice');
			}


		}else{
			$format=$this->readdata('format','text');
			$txt='please check the syntax. api specifications are here: http://www.freedesktop.org/wiki/Specifications/open-collaboration-services'."\n";
			$txt.=$this->getdebugoutput();
			echo($this->generatexml($format,'failed',999,$txt));
		}
		exit();
	}
	
	/**
	 * Use this function to inform the user that there is a syntax error in the API call. The function
	 * will inform the user which module the error occured in.
	 * @param apimodule The name of the module the error occured in
	 */
	private  function reportapisyntaxerror($apimodule){
		$format=$this->readdata('format','text');
		$txt='please check the syntax of the module '.$apimodule.'. api specifications are here: http://www.freedesktop.org/wiki/Specifications/open-collaboration-services'."\n";
		$txt.=$this->getdebugoutput();
		echo($this->generatexml($format,'failed',999,$txt));
	}

	/**
	 * generated some debug information to make it easier to find faild API calls
	 * @return debug data string
	 */
	private  function getdebugoutput() {
		$txt='';
		$txt.="debug output:\n";
		if(isset($_SERVER['REQUEST_METHOD'])) $txt.='http request method: '.$_SERVER['REQUEST_METHOD']."\n";
		if(isset($_SERVER['REQUEST_URI'])) $txt.='http request uri: '.$_SERVER['REQUEST_URI']."\n";
		if(isset($_GET)) foreach($_GET as $key=>$value) $txt.='get parameter: '.$key.'->'.$value."\n";
		if(isset($_POST)) foreach($_POST as $key=>$value) $txt.='post parameter: '.$key.'->'.$value."\n";
		return($txt);
	}

	/**
	 * checks if the user is authenticated
	 * checks the IP whitlist, apikeys and login/password combination
	 * if $forceuser is true and the authentication failed it returns an 401 http response. 
	 * if $forceuser is false and authentification fails it returns an empty username string
	 * @param bool $forceuser
	 * @return username string
	 */
	private  function checkpassword($forceuser=true) {

		// check whitelist
		if (in_array($_SERVER['REMOTE_ADDR'], $this->whitelist)) {
			$identifieduser='';
		}else{

			//valid user account ?
			if(isset($_SERVER['PHP_AUTH_USER'])) $authuser=$_SERVER['PHP_AUTH_USER']; else $authuser='';
			if(isset($_SERVER['PHP_AUTH_PW']))	 $authpw=$_SERVER['PHP_AUTH_PW']; else $authpw='';

			if(empty($authuser)) {
				if($forceuser){
					header('WWW-Authenticate: Basic realm="your valid user account or api key"');
					header('HTTP/1.0 401 Unauthorized');
					exit;
				}else{
					$identifieduser='';
				}
			}else{
				/*
				$user=H01_USER::finduserbyapikey($authuser,CONFIG_USERDB);
				if($user==false) {
				*/
					$user=$this->main->user->checklogin($authuser,$authpw);
					if($user==false) {
						if($forceuser){
							header('WWW-Authenticate: Basic realm="your valid user account or api key"');
							header('HTTP/1.0 401 Unauthorized');
							exit;
						}else{
							$identifieduser='';
						}
					}else{
						$identifieduser=$user;
					}
					/*
				}else{
					$identifieduser=$user;
				}*/
			}
		}
		return($identifieduser);
	}


	/**
	 * cleans up the api traffic limit database table.
	 * this function should be call by a cronjob every 15 minutes
	 */
	public  function cleanuptrafficlimit() {
		$this->main->db->q('truncate apitraffic');
	}



	/**
	 * check if the current user is allowed to do one more API call or if the traffic limit is exceeded.
	 * @param string $user
	 */
	private  function checktrafficlimit($user) {
		// BACKUP:
		// $result = $db->insert('apitraffic','into apitraffic (ip,count) values ('.ip2long($_SERVER['REMOTE_ADDR']).',1) on duplicate key update count=count+1');
		$this->main->db->q('insert into apitraffic (ip,count) values ('.ip2long($_SERVER['REMOTE_ADDR']).',1) on duplicate key update count=count+1');

		$result = $this->main->db->q('select * from apitraffic where ip="'.ip2long($_SERVER['REMOTE_ADDR']).'"');
		$numrows = $this->main->db->num_rows($result);
		$DBcount = $this->main->db->fetch_assoc($result);

		if($numrows==0) return(true);
		if($user=='') $max=$this->maxrequests; else $max=$this->maxrequestsauthenticated;

		if($DBcount['count']>$max) {
			$format=$this->readdata('format','text');
			echo($this->generatexml($format,'failed',200,'too many API requests in the last 15 minutes from your IP address. please try again later.'));
			exit();
		}
		return(true);

	}



	/**
	 * generates the xml or json response for the API call from an multidimenional data array.
	 * @param string $format
	 * @param string $status
	 * @param string $statuscode
	 * @param string $message
	 * @param array $data
	 * @param string $tag
	 * @param string $tagattribute
	 * @param int $dimension
	 * @param int $itemscount
	 * @param int $itemsperpage
	 * @return string xml/json
	 */
	private  function generatexml($format,$status,$statuscode,$message,$data=array(),$tag='',$tagattribute='',$dimension=-1,$itemscount='',$itemsperpage='') {
		if($format=='json') {

			$json=array();
			$json['status']=$status;
			$json['statuscode']=$statuscode;
			$json['message']=$message;
			$json['totalitems']=$itemscount;
			$json['itemsperpage']=$itemsperpage;
			$json['data']=$data;
			return(json_encode($json));


		}else{
			$txt='';
			$writer = xmlwriter_open_memory();
			xmlwriter_set_indent( $writer, 2 );
			xmlwriter_start_document($writer );
			xmlwriter_start_element($writer,'ocs');
			xmlwriter_start_element($writer,'meta');
			xmlwriter_write_element($writer,'status',$status);
			xmlwriter_write_element($writer,'statuscode',$statuscode);
			xmlwriter_write_element($writer,'message',$message);
			if($itemscount<>'') xmlwriter_write_element($writer,'totalitems',$itemscount);
			if(!empty($itemsperpage)) xmlwriter_write_element($writer,'itemsperpage',$itemsperpage);
			xmlwriter_end_element($writer);
//echo($dimension);
			if($dimension=='0') {
				// 0 dimensions
				xmlwriter_write_element($writer,'data',$data);

			}elseif($dimension=='1') {
				xmlwriter_start_element($writer,'data');
				foreach($data as $key=>$entry) {
					xmlwriter_write_element($writer,$key,$entry);
				}
				xmlwriter_end_element($writer);

			}elseif($dimension=='2') {
				xmlwriter_start_element($writer,'data');
				foreach($data as $entry) {
					xmlwriter_start_element($writer,$tag);
					if(!empty($tagattribute)) {
						xmlwriter_write_attribute($writer,'details',$tagattribute);
					}
					foreach($entry as $key=>$value) {
						if(is_array($value)){
							foreach($value as $k=>$v) {
								xmlwriter_write_element($writer,$k,$v);
							}
						} else {
							xmlwriter_write_element($writer,$key,$value);
						}
					}
					xmlwriter_end_element($writer);
				}
				xmlwriter_end_element($writer);

			}elseif($dimension=='3') {
				xmlwriter_start_element($writer,'data');
				foreach($data as $entrykey=>$entry) {
					xmlwriter_start_element($writer,$tag);
					if(!empty($tagattribute)) {
						xmlwriter_write_attribute($writer,'details',$tagattribute);
					}
					foreach($entry as $key=>$value) {
						if(is_array($value)){
							xmlwriter_start_element($writer,$entrykey);
							foreach($value as $k=>$v) {
								xmlwriter_write_element($writer,$k,$v);
							}
							xmlwriter_end_element($writer);
						} else {
							xmlwriter_write_element($writer,$key,$value);
						}
					}
					xmlwriter_end_element($writer);
				}
				xmlwriter_end_element($writer);
			}elseif($dimension=='dynamic') {
				xmlwriter_start_element($writer,'data');
//				$this->toxml($writer,$data,'comment');
				if(is_array($data)) $this->toxml($writer,$data,$tag);
				xmlwriter_end_element($writer);
			}

			xmlwriter_end_element($writer);

			xmlwriter_end_document( $writer );
			$txt.=xmlwriter_output_memory( $writer );
			unset($writer);
			return($txt);
		}
	}

	/**
	 * Take an array of any size, and make it into xml
	 * @param xmlwriter	An xmlwriter instance
	 * @param array			The array which is to be transformed
	 * @param mixed			Either a string, or an array of elements defining element names for each level in the XML hierarchy
	 *									 In the case of multiple lists of differently titled items at the same level, adding an array inside the array will allow for this to be constructed.
	 * @param int				Internal use (the index of the child item in question - corresponds to the index in the second level array above)
	 */
	public  function toxml($writer,$data,$node,$childindex=0) {
		$nodename=$node;
		if(is_array($node)){
			$nodename=array_shift($node);
		}

		$childcount=-1;
		foreach($data as $key => $value) {
			$childcount++;
			if (is_numeric($key)) {
				if(is_array($nodename)) {
					$key = $nodename[$childindex];
				} else {
					$key = $nodename;
				}
			}
			if (is_array($value)){
				xmlwriter_start_element($writer,$key);
				$this->toxml($writer,$value,$node,$childcount);
				xmlwriter_end_element($writer);
			}else{
				xmlwriter_write_element($writer,$key,$value);
			}
		}
		if(is_array($node)) {
			array_unshift($node,$nodename);
		}
	}




	/**
	 * return the config data of this server
	 * @param string $format
	 * @return string xml/json
	 */
	private  function apiconfig($format) {
		$user=$this->checkpassword(false);
		$this->checktrafficlimit($user);

		$xml['version']='1.6';
		$xml['website']='openDesktop.org';
		$xml['host']='api.openDesktop.org';
		$xml['contact']='frank@openDesktop.org';
		$xml['ssl']='true';
		echo($this->generatexml($format,'ok',100,'',$xml,'config','',1));
	}




	// PERSON API #############################################

	/**
	 * search and return a list of persons corresponding to different optional search parameters
	 * @param string $format
	 * @param string $username
	 * @param string $country
	 * @param string $city
	 * @param string $description
	 * @param string $pc
	 * @param string $software
	 * @param string $longitude
	 * @param string $latitude
	 * @param string $distance
	 * @param string $attributeapp
	 * @param string $attributekey
	 * @param string $attributevalue
	 * @param string $page
	 * @param string $pagesize
	 * @return string xml/json
	 */
	private  function personsearch($format,$username,$country,$city,$description,$pc,$software,$longitude,$latitude,$distance,$attributeapp,$attributekey,$attributevalue,$page,$pagesize) {
		$user=$this->checkpassword(false);
		$this->checktrafficlimit($user);

		if($pagesize==0) $pagesize=10; 
		$cache = new H01_CACHE('apipersonsearch',array($_SESSION['website'],$_SESSION['lang'],$format,$username.'#'.$user.'#'.$country.'#'.$city.'#'.$description.'#'.$pc.'#'.$software.'#'.$longitude.'#'.$latitude.'#'.$distance.'#'.$attributeapp.'#'.$attributekey.'#'.$attributevalue.'#'.$page.'#'.$pagesize));
		if ($cache->exist()) {
			$cache->get();
			unset($cache);
		} else {

			if($page>$this->maxpersonsearchpage) {
				$txt=$this->generatexml($format,'failed',102,'page above '.$this->maxpersonsearchpage.'. it is not allowed to fetch such a big resultset. please specify more search conditions.');
			}else{
				$xml=H01_USER::search($user,$username,$country,$city,$description,$pc,$software,$longitude,$latitude,$distance,$attributeapp,$attributekey,$attributevalue,$page,$pagesize);
				$usercount=$xml['usercount'];
				unset($xml['usercount']);
				$txt=$this->generatexml($format,'ok',100,'',$xml,'person','summary',2,$usercount,$pagesize);
			}

			$cache->put($txt);
			unset($cache);
			echo($txt);
		}

	}

	/**	 
	 * edit my own useraccount
	 * @param string $format
	 * @param string $country
	 * @param string $city
	 * @param float $longitude
	 * @param float $latitude
	 * @return string xml/json
	 */
	private  function personedit($format,$longitude,$latitude,$country,$city) {
		$user=$this->checkpassword();
		$this->checktrafficlimit($user);

		if($latitude<>0 or $longitude<>0 or !empty($city) or !empty($country)){
			H01_USER::edit($user,CONFIG_USERDB,$latitude,$longitude,$city,$country);

			// cleanup the caches for this user.
			H01_CACHEADMIN::cleancache('userdetail',array($user));
			H01_CACHEADMIN::cleancache('avatar',array($user));
			H01_CACHEADMIN::cleancache('apipersonget',array($user));
			H01_CACHEADMIN::cleancache('apipersonsearch',array());
			echo($this->generatexml($format,'ok',100,''));
		}else{
			echo($this->generatexml($format,'failed',101,'no parameters to update found'));
		}
	}


	/**	 
	 * register new user
	 * @param string $format
	 * @param string $login
	 * @param string $passwd
	 * @param string $firstname
	 * @param string $lastname
	 * @param string $email
	 * @return string xml/json
	 */
	private  function personadd($format,$login,$passwd,$firstname,$lastname,$email) {
		$user=$this->checkpassword(false);
		$this->checktrafficlimit($user);

		if($login<>'' and $passwd<>'' and $firstname<>'' and $lastname<>'' and $email<>''){
			if($this->main->user->isvalidpassword($passwd)){
				if($this->main->user->isloginname($login)){
					if(!$this->main->user->exists($login)){
						if($this->main->user->countusersbyemail($email)==0) {
							if($this->main->user->isvalidemail($email)) {
								$this->main->user->register($login,$passwd,$firstname,$lastname,$email);
								echo($this->generatexml($format,'ok',100,''));
							}else{
								echo($this->generatexml($format,'failed',106,'email already taken'));
							}
						}else{
							echo($this->generatexml($format,'failed',105,'email invalid'));
						}
					}else{
						echo($this->generatexml($format,'failed',104,'login already exists'));
					}
				}else{
					echo($this->generatexml($format,'failed',103,'please specify a valid login'));
				}
			}else{
				echo($this->generatexml($format,'failed',102,'please specify a valid password'));
			}
		}else{
			echo($this->generatexml($format,'failed',101,'please specify all mandatory fields'));
		}
	}

	/**	 
	 * TODO: fix personcheck
	 * check if the provided login/apikey/password is valid
	 * @param string $format
	 * @param string $login
	 * @param string $passwd
	 * @return string xml/json
	 */
	private  function personcheck($format,$login,$passwd) {
		$user=$this->checkpassword(false);
		$this->checktrafficlimit($user);


		if($login<>''){
			$reallogin=$this->main->user->checklogin($login,$passwd); // $login,CONFIG_USERDB,$passwd,PERM_Login
			if($reallogin<>false){
				$xml['person']['personid']=$reallogin;
				echo($this->generatexml($format,'ok',100,'',$xml,'person','check',2)); 
			}else{
				/*
				 * TODO: uncomment and implement login by API key
				$user=H01_USER::finduserbyapikey($login,CONFIG_USERDB);
				if($user==false) {
					*/
					echo($this->generatexml($format,'failed',102,'login not valid'));
					/*
				}else{
					$xml['person']['personid']=$user;
					echo($this->generatexml($format,'ok',100,'',$xml,'person','check',2)); 
					
				}
				*/
			}
		}else{
			echo($this->generatexml($format,'failed',101,'please specify all mandatory fields'));
		}
	}



	/**	 
	 * get detailed information about a person
	 * @param string $format
	 * @param string $username
	 * @return string xml/json
	 */
	private  function personget($format,$username='') {
		if(empty($username)) {
			$user=$this->checkpassword();
		}else{
			$user=$this->checkpassword(false);
		}
		$this->checktrafficlimit($user);
		if(empty($username)) $username=$user;

		$DBuser=$this->main->user->get_user_info($username);

		if(is_null($DBuser)){
			$txt=$this->generatexml($format,'failed',101,'person not found');
		}else{
			$xml=array();
			$xml[0]['personid']=$DBuser['login'];
			$xml[0]['firstname']=$DBuser['firstname'];
			$xml[0]['lastname']=$DBuser['lastname'];
			//$xml[0]['description']=H01_UTIL::bbcode2html($DBuser['description']);
			
			$txt=$this->generatexml($format,'ok',100,'',$xml,'person','full',2);
			//$txt=$this->generatexml($format,'failed',102,'data is private');
			echo($txt);
		}

	}


	/**	 
	 * get my own balance
	 * @param string $format
	 * @return string xml/json
	 */
	private  function persongetbalance($format) {
		$user=$this->checkpassword();
		$this->checktrafficlimit($user);

		$balance=H01_PAYMENT::getbalance($user,CONFIG_USERDB);

		$xml=array();
		$xml[0]['currency']='USD';
		$xml[0]['balance']=number_format(($balance/100),2);
		$txt=$this->generatexml($format,'ok',100,'',$xml,'person','balance',2); 
		echo($txt);
	}


	/**	 
	 * get attributes from a specific person/app/key
	 * @param string $format
	 * @param string $username
	 * @param string $app
	 * @param string $key
	 * @return string xml/json
	 */
	private  function personattributeget($format,$username,$app,$key)	{
		$user=$this->checkpassword();
		$this->checktrafficlimit($user);

		$xml=H01_USER::getattributes($username,CONFIG_USERDB,$app,$key);
		$xml2=array();
		$xml2['attribute']=$xml;
		$txt=$this->generatexml($format,'ok',100,'',$xml2,'person','attributes',3,count($xml)); 
		echo($txt);

	}

	/**	 
	 * set a attribute
	 * @param string $format
	 * @param string $app
	 * @param string $key
	 * @param string $value
	 * @return string xml/json
	 */
	private  function personattributeset($format,$app,$key,$value)	{
		$user=$this->checkpassword();
		$this->checktrafficlimit($user);

		$xml=H01_USER::setattribute($user,CONFIG_USERDB,$app,$key,$value);
		$txt=$this->generatexml($format,'ok',100,'');
		echo($txt);

	}


	/**	 
	 * delete a attribute
	 * @param string $format
	 * @param string $app
	 * @param string $key
	 * @return string xml/json
	 */
	private  function personattributedelete($format,$app,$key)	{
		$user=$this->checkpassword();
		$this->checktrafficlimit($user);

		$xml=H01_USER::deleteattribute($user,CONFIG_USERDB,$app,$key);
		$txt=$this->generatexml($format,'ok',100,'');
		echo($txt);

	}


	// FAN API #############################################

	/**	 
	 * get the fans of a specific content
	 * @param string $format
	 * @param string $content
	 * @param string $page
	 * @param string $pagesize
	 * @return string xml/json
	 */
	private  function fanget($format,$content,$page,$pagesize) {
		$user=$this->checkpassword();
		$this->checktrafficlimit($user);
		$content=strip_tags(addslashes($content));
		$page = intval($page);
		$start=$pagesize*$page;

		$cache = new H01_CACHE('apifan',array($content,CONFIG_USERDB,$page,$pagesize,$format));
		if ($cache->exist()) {
			$cache->get();
			unset($cache);
		} else {

			$fancount=H01_FAN::countfansofcontent($content,CONFIG_USERDB);
			$fans=H01_FAN::getfansofcontent($content,CONFIG_USERDB,$page,$pagesize);
			$itemscount=count($fans);
			$xml=array();
			for ($i=0; $i < $itemscount;$i++) {
				$xml[$i]['personid']=$fans[$i]['user'];
				$xml[$i]['timestamp']=date('c',$fans[$i]['timestamp']);
			}
			$txt=$this->generatexml($format,'ok',100,'',$xml,'person','fans',2,$fancount,$pagesize);

			$cache->put($txt);
			unset($cache);
			echo($txt);
		}

	}


	/**	 
	 * add a fans to a specific content
	 * @param string $format
	 * @param string $content
	 * @return string xml/json
	 */
	private  function addfan($format,$content) {
		$contentid = intval($content);
		$user=$this->checkpassword(true);
		$this->checktrafficlimit($user);
		
		$fan = new OCSFan;
		$fan->add($contentid);
		
		$txt=$this->generatexml($format,'ok',100,'');
		echo($txt);
	}


	/**	 
	 * remove a fans from a specific content
	 * @param string $format
	 * @param string $content
	 * @return string xml/json
	 */
	private  function removefan($format,$content) {
		$contentid = intval($content);
		$user=$this->checkpassword();
		$this->checktrafficlimit($user);
		H01_FAN::removefan($contentid,$user,CONFIG_USERDB);
		$txt=$this->generatexml($format,'ok',100,'');
		echo($txt);
	}
 
 
	/**	 
	 * check if the user is a fan of a content
	 * @param string $format
	 * @param string $content
	 * @return string xml/json
	 */
	private  function isfan($format,$content) {
		$contentid = intval($content);
		$user=$this->checkpassword(true);
		$this->checktrafficlimit($user);
		$fan = new OCSFan;
		if($fan->isfan($contentid)){
			$xml['status']='fan';
			$txt=$this->generatexml($format,'ok',100,'',$xml,'','',1); 
		}else{
			$xml['status']='not fan';
			$txt=$this->generatexml($format,'ok',100,'',$xml,'','',1); 
		}
		echo($txt);
	}





	// FRIEND API #############################################

	/**	 
	 * get the list of sent invitations
	 * @param string $format
	 * @param string $page
	 * @param string $pagesize
	 * @return string xml/json
	 */
	private  function friendsentinvitations($format,$page,$pagesize) {
		$user=$this->checkpassword();
		$this->checktrafficlimit($user);

		$fromuser=addslashes($user);
		$page = intval($page);
		$start=$pagesize*$page;
		$count=$pagesize;

		$cache = new H01_CACHE('apifriendssentinvitations',array($fromuser,CONFIG_USERDB,$page,$pagesize,$format));
		if ($cache->exist()) {
			$cache->get();
			unset($cache);
		} else {

			$countsentinvitations=H01_RELATION::countsentrequests(1,$fromuser,CONFIG_USERDB);
			$relations=H01_RELATION::getsentrequests(1,$fromuser,CONFIG_USERDB,$start,$count);
			$itemscount=count($relations);

			$xml=array();
			for ($i=0; $i < $itemscount;$i++) {
				$xml[$i]['personid']=$relations[$i]['user'];
			}
			$txt=$this->generatexml($format,'ok',100,'',$xml,'user','id',2,$countsentinvitations,$pagesize);

			$cache->put($txt);
			unset($cache);
			echo($txt);
		}
	}

	/**	 
	 * get the list of received invitations
	 * @param string $format
	 * @param string $page
	 * @param string $pagesize
	 * @return string xml/json
	 */
	private  function friendreceivedinvitations($format,$page,$pagesize) {
		$user=$this->checkpassword();
		$this->checktrafficlimit($user);

		$fromuser=addslashes($user);
		$page = intval($page);
		$start=$pagesize*$page;
		$count=$pagesize;

		$cache = new H01_CACHE('apifriendsreceivedinvitations',array($fromuser,CONFIG_USERDB,$page,$pagesize,$format));
		if ($cache->exist()) {
			$cache->get();
			unset($cache);
		} else {

			$countreceivedinvitations=H01_RELATION::countreceivedrequests(1,$fromuser,CONFIG_USERDB);
			$relations=H01_RELATION::getreceivedrequests(1,$fromuser,CONFIG_USERDB,$start,$count);
			$itemscount=count($relations);
			$xml=array();
			for ($i=0; $i < $itemscount;$i++) {
				$xml[$i]['personid']=$relations[$i]['user'];
			}
			$txt=$this->generatexml($format,'ok',100,'',$xml,'user','id',2,$countreceivedinvitations,$pagesize);

			$cache->put($txt);
			unset($cache);
			echo($txt);
		}
	}



	/**	 
	 * get the list of friends from a person
	 * @param string $format
	 * @param string $fromuser
	 * @param string $page
	 * @param string $pagesize
	 * @return string xml/json
	 */
	private  function friendget($format,$fromuser,$page,$pagesize) {
		$user=$this->checkpassword();
		$this->checktrafficlimit($user);

		$fromuser=strip_tags(addslashes($fromuser));
		$page = intval($page);
		$start=$pagesize*$page;
		$count=$pagesize;

		$cache = new H01_CACHE('apifriends',array($fromuser,CONFIG_USERDB,$page,$pagesize,$format));
		if ($cache->exist()) {
			$cache->get();
			unset($cache);
		} else {

			$DBuser=H01_USER::getuser($fromuser,CONFIG_USERDB);
			if(isset($DBuser['login'])) {
				if($DBuser['privacyrelations']==0) {
					$visible=true;
				}elseif($DBuser['privacyrelations']==1){
					if($user<>'') $visible=true; else $visible=false;
				}elseif($DBuser['privacyrelations']==2){
					if(($fromuser==$user) or (H01_RELATION::isrelation(1,$fromuser,CONFIG_USERDB,$user))) $visible=true; else $visible=false;
				}elseif($DBuser['privacyrelations']==3){
					if($fromuser==$user) $visible=true; else $visible=false;
				}

			 if($visible){
					$countapprovedrelations=H01_RELATION::countapprovedrelations(1,$fromuser,CONFIG_USERDB);
					$relations=H01_RELATION::getapprovedrelations(1,$fromuser,CONFIG_USERDB,$start,$count,true);
					$itemscount=count($relations);
					$xml=array();
					for ($i=0; $i < $itemscount;$i++) {
						$xml[$i]['personid']=$relations[$i]['user'];
						$xml[$i]['firstname']=$relations[$i]['firstname'];
						$xml[$i]['lastname']=$relations[$i]['lastname'];


						if		 (file_exists(CONFIG_DOCUMENT_ROOT.'/CONTENT/user-pics/'.CONFIG_USERDB.'/'.$relations[$i]['user'].'.jpg')) { $pic='http://'.CONFIG_WEBSITEHOST.'/CONTENT/user-pics/'.CONFIG_USERDB.'/'.$relations[$i]['user'].'.jpg'; $found=true; }
						elseif (file_exists(CONFIG_DOCUMENT_ROOT.'/CONTENT/user-pics/'.CONFIG_USERDB.'/'.$relations[$i]['user'].'.png')) { $pic='http://'.CONFIG_WEBSITEHOST.'/CONTENT/user-pics/'.CONFIG_USERDB.'/'.$relations[$i]['user'].'.png'; $found=true; }
						elseif (file_exists(CONFIG_DOCUMENT_ROOT.'/CONTENT/user-pics/'.CONFIG_USERDB.'/'.$relations[$i]['user'].'.gif')) { $pic='http://'.CONFIG_WEBSITEHOST.'/CONTENT/user-pics/'.CONFIG_USERDB.'/'.$relations[$i]['user'].'.gif'; $found=true; }
						else	{	$pic=HOST.'/usermanager/nopic.png'; $found=false ;}
						$xml[$i]['avatarpic']=$pic;
						$xml[$i]['avatarpicfound']=$found;


					}
					$txt=$this->generatexml($format,'ok',100,'',$xml,'user','id',2,$countapprovedrelations,$pagesize);
				}else{
					$txt=$this->generatexml($format,'failed',101,'data is private');
				}
			}else{
				$txt=$this->generatexml($format,'failed',102,'user not found');
			}

			$cache->put($txt);
			unset($cache);
			echo($txt);
		}

	}




	/**	 
	 * invite a person as a friend
	 * @param string $format
	 * @param string $inviteuser
	 * @param string $message
	 * @return string xml/json
	 */
	private  function friendinvite($format,$inviteuser,$message) {
		$user=$this->checkpassword();
		$this->checktrafficlimit($user);
		$inviteuser = strip_tags(addslashes($inviteuser));
		$message = strip_tags(addslashes($message));

		$u=H01_USER::getuser($inviteuser,CONFIG_USERDB);
		if($u==false) $inviteuser=false; else $inviteuser=$u['login'];

		if($user<>'' and $inviteuser<>'' and $inviteuser<>false) {
			if($user<>$inviteuser) {
				if($message<>'') {
					H01_RELATION::requestrelation(1,$user,$inviteuser,CONFIG_USERDB,$message);
					echo($this->generatexml($format,'ok',100,''));
				} else {
					echo($this->generatexml($format,'failed',101,'message must not be empty'));
				}
			}else{
				echo($this->generatexml($format,'failed',102,'you can\´t invite yourself'));
			}
		} else {
			echo($this->generatexml($format,'failed',103,'user not found'));
		}

	}

	/**	 
	 * approve a friendsship invitation
	 * @param string $format
	 * @param string $inviteuser
	 * @return string xml/json
	 */
	private  function friendapprove($format,$inviteuser) {
		$user=$this->checkpassword();
		$this->checktrafficlimit($user);
		$inviteuser = strip_tags(addslashes($inviteuser));

		if($user<>'' and $inviteuser<>'') {
			H01_RELATION::confirmrelation(1,$user,$inviteuser,CONFIG_USERDB);
			echo($this->generatexml($format,'ok',100,''));
		} else {
			echo($this->generatexml($format,'failed',101,'user not found'));
		}

	}


	/**	 
	 * decline a friendsship invitation
	 * @param string $format
	 * @param string $inviteuser
	 * @return string xml/json
	 */
	private  function frienddecline($format,$inviteuser) {
		$user=$this->checkpassword();
		$this->checktrafficlimit($user);
		$inviteuser = strip_tags(addslashes($inviteuser));

		if($user<>'' and $inviteuser<>'') {
			H01_RELATION::declinerelation(1,$user,$inviteuser,CONFIG_USERDB);
			echo($this->generatexml($format,'ok',100,''));
		} else {
			echo($this->generatexml($format,'failed',101,'user not found'));
		}

	}


	/**	 
	 * cancel a friendsship
	 * @param string $format
	 * @param string $inviteuser
	 * @return string xml/json
	 */
	private  function friendcancel($format,$inviteuser) {
		$user=$this->checkpassword();
		$this->checktrafficlimit($user);
		$inviteuser = strip_tags(addslashes($inviteuser));

		if($user<>'' and $inviteuser<>'') {
			H01_RELATION::cancelrelation(1,$user,$inviteuser,CONFIG_USERDB);
			echo($this->generatexml($format,'ok',100,''));
		} else {
			echo($this->generatexml($format,'failed',101,'user not found'));
		}

	}


	/**	 
	 * cancel a friendsship invitation
	 * @param string $format
	 * @param string $inviteuser
	 * @return string xml/json
	 */
	private  function friendcancelrequest($format,$inviteuser) {
		$user=$this->checkpassword();
		$this->checktrafficlimit($user);
		$inviteuser = strip_tags(addslashes($inviteuser));

		if($user<>'' and $inviteuser<>'') {
			H01_RELATION::deleterelationrequest(1,$user,$inviteuser,CONFIG_USERDB);
			echo($this->generatexml($format,'ok',100,''));
		} else {
			echo($this->generatexml($format,'failed',101,'user not found'));
		}

	}






	// MESSAGE API #############################################

	/**	 
	 * get the list of available message foldersn
	 * @param string $format
	 * @return string xml/json
	 */
	private  function messagefolders($format) {
		$user=$this->checkpassword();
		$this->checktrafficlimit($user);
		if(!empty($user)) {
			$cache = new H01_CACHE('apimessagefolder',array($user,CONFIG_USERDB,$format));
			if ($cache->exist()) {
				$cache->get();
				unset($cache);
			} else {

				$i=0;
				foreach(H01_MESSAGE::$FOLDERS[1] as $key=>$value) {
					$i++;
					$xml[$i]['id']=$key;
					$xml[$i]['name']=$value;
					$count=H01_MESSAGE::countmessages($user,CONFIG_USERDB,$key);
					$xml[$i]['messagecount']=$count;
					if($key==0) $xml[$i]['type']='inbox';
					elseif($key==1) $xml[$i]['type']='send';
					elseif($key==2) $xml[$i]['type']='trash';
					else $xml[$i]['type']='';
				}
				$txt=$this->generatexml($format,'ok',100,'',$xml,'folder','',2,count(H01_MESSAGE::$FOLDERS[1]));

				$cache->put($txt);
				unset($cache);
				echo($txt);
			}

		}else{
			$txt=$this->generatexml($format,'failed',101,'user not found');
			echo($txt);
		}

	}


	/**	 
	 * get a list of messages
	 * @param string $format
	 * @param string $folder
	 * @param string $page
	 * @param string $pagesize
	 * @param string $filter
	 * @return string xml/json
	 */
	private  function messagelist($format,$folder,$page,$pagesize,$filter) {
		$user=$this->checkpassword();
		$this->checktrafficlimit($user);

		$cache = new H01_CACHE('apimessagelist',array($user,CONFIG_USERDB,$folder,$filter,$page,$pagesize,$format));
		if ($cache->exist()) {
			$cache->get();
			unset($cache);
		} else {
			$messages=H01_MESSAGE::getlist($user,CONFIG_USERDB,$folder,$page,$pagesize,$filter);
			$messagescount=$messages['count'];
			unset($messages['count']);
			$itemscount=count($messages);
			$xml=array();
			for ($i=0; $i < $itemscount;$i++) {
				$xml[$i]['id']=$messages[$i]['id'];
				$xml[$i]['messagefrom']=$messages[$i]['messagefrom'];
				$xml[$i]['firstname']=$messages[$i]['firstname'];
				$xml[$i]['lastname']=$messages[$i]['lastname'];
				$xml[$i]['profilepage']='http://'.CONFIG_WEBSITEHOST.'/usermanager/search.php?username='.urlencode($messages[$i]['messagefrom']); 
				$xml[$i]['messageto']=$messages[$i]['messageto'];
				$xml[$i]['senddate']=date('c',$messages[$i]['senddate']);
				$xml[$i]['status']=$messages[$i]['status'];
				$xml[$i]['statustext']=strip_tags(H01_MESSAGE::$STATUS[1][$messages[$i]['status']]);
				$xml[$i]['subject']=$messages[$i]['subject'];
				$xml[$i]['body']=$messages[$i]['body'];
//				$xml[$i]['folder']=$messages[$i]['folder'];
			}

			$txt=$this->generatexml($format,'ok',100,'',$xml,'message','full',2,$messagescount,$pagesize);

			$cache->put($txt);
			unset($cache);
			echo($txt);
		}
	}

	/**	 
	 * get one specific message
	 * @param string $format
	 * @param string $folder
	 * @param string $message
	 * @return string xml/json
	 */
	private  function messageget($format,$folder,$message) {
		$user=$this->checkpassword();
		$this->checktrafficlimit($user);

		$cache = new H01_CACHE('apimessageget',array($user,CONFIG_USERDB,$folder,$message,$format));
		if ($cache->exist()) {
			$cache->get();
			unset($cache);
		} else {

			H01_MESSAGE::setstatus($message,$user,CONFIG_USERDB,1); 
			$message=H01_MESSAGE::get($user,CONFIG_USERDB,$folder,$message);
			if(count($message)>0) {
				$xml['id']=$message['id'];
				$xml['messagefrom']=$message['messagefrom'];
				$xml['firstname']=$message['firstname'];
				$xml['lastname']=$message['lastname'];
				$xml['profilepage']='http://'.CONFIG_WEBSITEHOST.'/usermanager/search.php?username='.urlencode($message['messagefrom']); 
				$xml['messageto']=$message['messageto'];
				$xml['senddate']=date('c',$message['senddate']);
				$xml['status']=$message['status'];
				$xml['statustext']=strip_tags(H01_MESSAGE::$STATUS[1][$message['status']]);
				$xml['subject']=$message['subject'];
				$xml['body']=$message['body'];
				$xml2[1]=$xml;
				$txt=$this->generatexml($format,'ok',100,'',$xml2,'message','full',2);
			}else{
				$txt=$this->generatexml($format,'failed',101,'message not found');
			}

			$cache->put($txt);
			unset($cache);
			echo($txt);
		}
	}



	/**	 
	 * send a message
	 * @param string $format
	 * @param string $touser
	 * @param string $subject
	 * @param string $message
	 * @return string xml/json
	 */
	private  function messagesend($format,$touser,$subject,$message) {
		$user=$this->checkpassword();
		$this->checktrafficlimit($user);

		if($touser<>$user) {
			if(!empty($subject) and !empty($message)) {
				if(!empty($user) and H01_USER::exist($touser,CONFIG_USERDB,true)) {
					H01_MESSAGE::send($user,CONFIG_USERDB,$touser,$subject,$message);
					echo($this->generatexml($format,'ok',100,''));
				}else{
					echo($this->generatexml($format,'failed',101,'user not found'));
				}
			}else{
				echo($this->generatexml($format,'failed',102,'subject or message not found'));
			}
		}else{
			echo($this->generatexml($format,'failed',103,'you can\´t send a message to yourself'));
		}
	}


	// ACTIVITY API #############################################

	/**	 
	 * get my activities
	 * @param string $format
	 * @param string $page
	 * @param string $pagesize
	 * @return string xml/json
	 */
	private  function activityget($format,$page,$pagesize) {

		$user=$this->checkpassword();
		$this->checktrafficlimit($user);
		
		$cache = new H01_CACHE('apilog',array($user,CONFIG_USERDB,$page,$pagesize,$format));
		if ($cache->exist()) {
			$cache->get();
			unset($cache);
		} else {

			$log=H01_LOG::getlist($user,CONFIG_USERDB,$page,$pagesize);
			$totalcount=$log['count'];
			unset($log['count']);
			$itemscount=count($log);
			$xml=array();
			for ($i=0; $i < $itemscount;$i++) {
				$xml[$i]['id']=$log[$i]['id'];
				$xml[$i]['personid']=$log[$i]['user'];
				$xml[$i]['firstname']=$log[$i]['firstname'];
				$xml[$i]['lastname']=$log[$i]['name'];
				$xml[$i]['profilepage']='http://'.CONFIG_WEBSITEHOST.'/usermanager/search.php?username='.urlencode($log[$i]['user']); 

				if		 (file_exists(CONFIG_DOCUMENT_ROOT.'/CONTENT/user-pics/'.CONFIG_USERDB.'/'.$log[$i]['user'].'.jpg')) $pic='http://'.CONFIG_WEBSITEHOST.'/CONTENT/user-pics/'.CONFIG_USERDB.'/'.$log[$i]['user'].'.jpg';
				elseif (file_exists(CONFIG_DOCUMENT_ROOT.'/CONTENT/user-pics/'.CONFIG_USERDB.'/'.$log[$i]['user'].'.png')) $pic='http://'.CONFIG_WEBSITEHOST.'/CONTENT/user-pics/'.CONFIG_USERDB.'/'.$log[$i]['user'].'.png';
				elseif (file_exists(CONFIG_DOCUMENT_ROOT.'/CONTENT/user-pics/'.CONFIG_USERDB.'/'.$log[$i]['user'].'.gif')) $pic='http://'.CONFIG_WEBSITEHOST.'/CONTENT/user-pics/'.CONFIG_USERDB.'/'.$log[$i]['user'].'.gif';
				else	 $pic='http://'.CONFIG_WEBSITEHOST.'/usermanager/nopic.png';
				$xml[$i]['avatarpic']=$pic;

				$xml[$i]['timestamp']=date('c',$log[$i]['timestamp']);
				$xml[$i]['type']=$log[$i]['type'];
				$xml[$i]['message']=strip_tags($log[$i]['logmessage']);
				$xml[$i]['link']=$log[$i]['link'];
			}

			$txt=$this->generatexml($format,'ok',100,'',$xml,'activity','full',2,$totalcount,$pagesize);

			$cache->put($txt);
			unset($cache);
			echo($txt);
		}

	}

	/**	 
	 * submit a activity
	 * @param string $format
	 * @param string $message
	 * @return string xml/json
	 */
	private  function activityput($format,$message) {
		$user=$this->checkpassword();
		$this->checktrafficlimit($user);

		if($user<>'') {
			if(trim($message)<>'') {
				H01_MICROBLOG::send($user,CONFIG_USERDB,$message);
				echo($this->generatexml($format,'ok',100,''));
			} else {
				echo($this->generatexml($format,'failed',101,'empty message'));
			}
		} else {
			echo($this->generatexml($format,'failed',102,'user not found'));
		}

	}


	// CONTENT API #############################################

	/**	 
	 * get a specific content
	 * @param string $format
	 * @param string $content
	 * @return string xml/json
	 */
	private function contentget($format,$content) {

		$user=$this->checkpassword(false);
		$this->checktrafficlimit($user);

		$content=addslashes($content);
		
		// fetch data
		$con = new OCSContent();

		// check data
		if (!$con->load($content)) {
			$txt=$this->generatexml($format,'failed',101,'content not found');
		} else {

			$xml['id']=$con->id;
			$xml['name']=$con->name;
			$xml['version']=$con->version;
			//$xml['typeid']=$con['type'];
			//$xml['typename']=$WEBSITECONTENT[$con['type']];
			//$xml['language']=H01_CONTENT::$LANGUAGES[$con['language']];
			$xml['personid']=$con->owner;
			//$xml['profilepage']='http://opendesktop.org/usermanager/search.php?username='.urlencode($con['user']);
			//$xml['created']=date('c',$con['created']);
			//$xml['changed']=date('c',$con['changed']);
			//$xml['downloads']=$con['downloads'];
			$xml['score'] = $con->score;
			$xml['description'] = $con->description;
			$xml['summary'] = $con->summary;
			//$xml['feedbackurl'] = $con['feedbackurl'];
			$xml['changelog'] = $con->changelog;
			/*$xml['homepage'] = $con['homepage1'];
			if($con['homepagetype1']<>0) $xml['homepagetype']=H01_CONTENT::$LINK_CATEGORY[$con['homepagetype1']]; else $xml['homepagetype']='';
			$xml['homepage2']=$con['homepage2'];
			if($con['homepagetype2']<>0) $xml['homepagetype2']=H01_CONTENT::$LINK_CATEGORY[$con['homepagetype2']]; else $xml['homepagetype2']='';
			$xml['homepage3']=$con['homepage3'];
			if($con['homepagetype3']<>0) $xml['homepagetype3']=H01_CONTENT::$LINK_CATEGORY[$con['homepagetype3']]; else $xml['homepagetype3']='';
			$xml['homepage4']=$con['homepage4'];
			if($con['homepagetype4']<>0) $xml['homepagetype4']=H01_CONTENT::$LINK_CATEGORY[$con['homepagetype4']]; else $xml['homepagetype4']='';
			$xml['homepage5']=$con['homepage5'];
			if($con['homepagetype5']<>0) $xml['homepagetype5']=H01_CONTENT::$LINK_CATEGORY[$con['homepagetype5']]; else $xml['homepagetype5']='';
			$xml['homepage6']=$con['homepage6'];
			if($con['homepagetype6']<>0) $xml['homepagetype6']=H01_CONTENT::$LINK_CATEGORY[$con['homepagetype6']]; else $xml['homepagetype6']='';
			$xml['homepage7']=$con['homepage7'];
			if($con['homepagetype7']<>0) $xml['homepagetype7']=H01_CONTENT::$LINK_CATEGORY[$con['homepagetype7']]; else $xml['homepagetype7']='';
			$xml['homepage8']=$con['homepage8'];
			if($con['homepagetype8']<>0) $xml['homepagetype8']=H01_CONTENT::$LINK_CATEGORY[$con['homepagetype8']]; else $xml['homepagetype8']='';
			$xml['homepage9']=$con['homepage9'];
			if($con['homepagetype9']<>0) $xml['homepagetype9']=H01_CONTENT::$LINK_CATEGORY[$con['homepagetype9']]; else $xml['homepagetype9']='';
			$xml['homepage10']=$con['homepage10'];
			if($con['homepagetype10']<>0) $xml['homepagetype10']=H01_CONTENT::$LINK_CATEGORY[$con['homepagetype10']]; else $xml['homepagetype10']='';
			*/

			//$xml['licensetype']=$con->license;
			/*if (($con['licensetype']<>0) and ($con['licensetype']<>1000)) {
				if(isset($contentlicense[$con['licensetype']])) $xml['license']=$contentlicense[$con['licensetype']];
			} else {
				if (!empty($con['license'])) $xml['license']=nl2br(htmlspecialchars($con['license']));
			}
			$xml['license'] = $con->license;
			
			if(!empty($con['donation'])) $xml['donationpage']='http://'.CONFIG_WEBSITEHOST.'/content/donate.php?content='.$con['id']; else $xml['donationpage']='';
			$xml['comments']=$con['commentscount'];
			$xml['commentspage']='http://'.CONFIG_WEBSITEHOST.'/content/show.php?content='.$con['id'];
			$xml['fans']=$con['fancount'];
			$xml['fanspage']='http://'.CONFIG_WEBSITEHOST.'/content/show.php?action=fan&content='.$con['id'];
			$xml['knowledgebaseentries']=$con['knowledgebasecount'];
			$xml['knowledgebasepage']='http://'.CONFIG_WEBSITEHOST.'/content/show.php?action=knowledgebase&content='.$con['id'];
			
			if ($con['depend']<>0) $xml['depend']=$DEPENDTYPES[$con['depend']]; else $xml['depend']='';
			
			// preview
			if (!empty($con['preview1'])) $pic1=$con['id'].'-1.'.$con['preview1']; else $pic1='';
			if (!empty($con['preview2'])) $pic2=$con['id'].'-2.'.$con['preview2']; else $pic2='';
			if (!empty($con['preview3'])) $pic3=$con['id'].'-3.'.$con['preview3']; else $pic3='';
			if (!empty($con['preview1'])) $picsmall1='m'.$con['id'].'-1.png'; else $picsmall1='';
			if (!empty($con['preview2'])) $picsmall2='m'.$con['id'].'-2.png'; else $picsmall2='';
			if (!empty($con['preview3'])) $picsmall3='m'.$con['id'].'-3.png'; else $picsmall3='';
			
			
			if(!empty($pic1)) $xml['preview1']='http://'.CONFIG_WEBSITEHOST.'/content/preview.php?preview=1&id='.$con['id'].'&file1='.$pic1.'&file2='.$pic2.'&file3='.$pic3.'&name='.urlencode($con['name']); else $xml['preview1']='';
			if(!empty($pic2)) $xml['preview2']='http://'.CONFIG_WEBSITEHOST.'/content/preview.php?preview=2&id='.$con['id'].'&file1='.$pic1.'&file2='.$pic2.'&file3='.$pic3.'&name='.urlencode($con['name']); else $xml['preview2']='';
			if(!empty($pic3)) $xml['preview3']='http://'.CONFIG_WEBSITEHOST.'/content/preview.php?preview=3&id='.$con['id'].'&file1='.$pic1.'&file2='.$pic2.'&file3='.$pic3.'&name='.urlencode($con['name']); else $xml['preview3']='';
			if(!empty($pic1)) $xml['previewpic1']='http://'.CONFIG_WEBSITEHOST.'/CONTENT/content-pre1/'.$pic1; else $xml['previewpic1']='';
			if(!empty($pic2)) $xml['previewpic2']='http://'.CONFIG_WEBSITEHOST.'/CONTENT/content-pre2/'.$pic2; else $xml['previewpic2']='';
			if(!empty($pic3)) $xml['previewpic3']='http://'.CONFIG_WEBSITEHOST.'/CONTENT/content-pre3/'.$pic3; else $xml['previewpic3']='';
			if(!empty($picsmall1)) $xml['smallpreviewpic1']='http://'.CONFIG_WEBSITEHOST.'/CONTENT/content-m1/'.$picsmall1; else $xml['picsmall1']='';
			if(!empty($picsmall2)) $xml['smallpreviewpic2']='http://'.CONFIG_WEBSITEHOST.'/CONTENT/content-m2/'.$picsmall2; else $xml['picsmall2']='';
			if(!empty($picsmall3)) $xml['smallpreviewpic3']='http://'.CONFIG_WEBSITEHOST.'/CONTENT/content-m3/'.$picsmall3; else $xml['picsmall3']='';
			$xml['detailpage']='http://'.CONFIG_WEBSITEHOST.'/content/show.php?content='.$con['id'];
			*/
			// download
			if (!empty($con->downloadname1) or !empty($con->downloadlink1)) {
				/*
				if($con['downloadfiletype1']<>0) {
					$typetmp=$DISTRIBUTIONSTYPES[$con['downloadfiletype1']].' ';
				} else {
					$typetmp='';
				}
				$xml['downloadtype1']=$typetmp;
				if($con['downloadbuy1']==1) {
					$xml['downloadprice1']=$con['downloadbuyprice1'];
					$xml['downloadlink1']='http://'.CONFIG_WEBSITEHOST.'/content/buy.php?content='.$con['id'].'&id=1';
				}else{
					$xml['downloadprice1']='0';
					$xml['downloadlink1']='http://'.CONFIG_WEBSITEHOST.'/content/download.php?content='.$con['id'].'&id=1';
				}
				*/
				$xml['downloadname1'] = $con->downloadname1;
				$xml['downloadlink1'] = $con->downloadlink1;
				/*
				if(!empty($con['downloadgpgfingerprint1'])) $xml['downloadgpgfingerprint1']=$con['downloadgpgfingerprint1']; else $xml['downloadgpgfingerprint1']='';
				if(!empty($con['downloadgpgsignature1']))	 $xml['downloadgpgsignature1']=$con['downloadgpgsignature1'];		 else $xml['downloadgpgsignature1']='';
				if(!empty($con['downloadpackagename1'])) $xml['downloadpackagename1']=$con['downloadpackagename1']; else $xml['downloadpackagename1']='';
				if(!empty($con['downloadrepository1'])) $xml['downloadrepository1']=$con['downloadrepository1']; else $xml['downloadrepository1']='';
				
				if(($con['downloadtyp1']=='0') and (!empty($con['download1']))) $xml['downloadsize1']=ceil(@filesize(CONFIG_DOCUMENT_ROOT.'/CONTENT/content-files/'.$con['download1'])/1024); else $xml['downloadsize1']='';
				*/
			} else {
				$xml['downloadname1']='';
				$xml['downloadlink1']='';
			}
			
			/*
			for ($i=2; $i <= 12;$i++) {
				if (!empty($con['downloadname'.$i]) and !empty($con['downloadlink'.$i]) ) {
					if($con['downloadfiletype'.$i]<>0) {
						$typetmp=$DISTRIBUTIONSTYPES[$con['downloadfiletype'.$i]].' ';
					} else {
						$typetmp='';
					}
					$xml['downloadtype'.$i]=$typetmp;

					if($con['downloadbuy'.$i]==1) {
						$xml['downloadprice'.$i]=$con['downloadbuyprice'.$i];
						$xml['downloadlink'.$i]='http://'.CONFIG_WEBSITEHOST.'/content/buy.php?content='.$con['id'].'&id='.$i;
					}else{
						$xml['downloadprice'.$i]='0';
						$xml['downloadlink'.$i]='http://'.CONFIG_WEBSITEHOST.'/content/download.php?content='.$con['id'].'&id='.$i;
					}
					if(!empty($con['downloadname'.$i])) $xml['downloadname'.$i]=$con['downloadname'.$i]; else $xml['downloadname'.$i]='';
					if(!empty($con['downloadgpgfingerprint'.$i])) $xml['downloadgpgfingerprint'.$i]=$con['downloadgpgfingerprint'.$i]; else $xml['downloadgpgfingerprint'.$i]='';
					if(!empty($con['downloadgpgsignature'.$i])) $xml['downloadgpgsignature'.$i]=$con['downloadgpgsignature'.$i]; else $xml['downloadgpgsignature'.$i]='';
					if(!empty($con['downloadpackagename'.$i])) $xml['downloadpackagename'.$i]=$con['downloadpackagename'.$i]; else $xml['downloadpackagename'.$i]='';
					if(!empty($con['downloadrepository'.$i])) $xml['downloadrepository'.$i]=$con['downloadrepository'.$i]; else $xml['downloadrepository'.$i]='';
				}
			}
			*/
			$xml2[0]=$xml;
			$txt=$this->generatexml($format,'ok',100,'',$xml2,'content','full',2);
			echo($txt);

		}

	}



	/**	 
	 * get the download link for a content
	 * @param string $format
	 * @param string $content
	 * @param string $item
	 * @return string xml/json
	 */
	 private  function contentdownload($format,$content,$item) {
			$user=$this->checkpassword(false);
			$this->checktrafficlimit($user);

			$content = (int) $content;
			$item = (int) $item;

			// item range
			if($item<1 or $item>12) {
				$txt=$this->generatexml($format,'failed',103,'item not found');
			} else {

				// fetch data
				$con = new OCSContent();

				// check data
				if (!$con->load($content)) {
					$txt=$this->generatexml($format,'failed',101,'content not found');
				} else {
						//download link
						$link = $con->downloadlink1;
						//mimetype
						$headers = get_headers($link);
						$mimetype = $headers[3];
						
						if (!empty($con->downloadname1) or !empty($con->downloadlink1)) {
							$xml['downloadlink']=$link;
							$xml['mimetype']=$mimetype;
							$xml2[0]=$xml;
							$txt=$this->generatexml($format,'ok',100,'',$xml2,'content','download',2);
						} else {
							$txt=$this->generatexml($format,'failed',103,'content item not found');
						}
				
				}

			if(isset($txt) and $txt<>'') {
				echo($txt);
			}
		}
	}





	/**	 
	 * get a list of contents
	 * @param string $format
	 * @param string $contents
	 * @param string $searchstr
	 * @param string $searchuser
	 * @param string $sortmode
	 * @param string $page
	 * @param string $pagesize
	 * @return string xml/json
	 */
	private  function contentlist($format,$contents,$searchstr,$searchuser,$external,$distribution,$license,$sortmode,$page,$pagesize) {
		//category -> ignore
		//sortmode -> 
		//page     ->
		//pagesize ->
		$user=$this->checkpassword(false);
		$this->checktrafficlimit($user);
		
		$conl = new OCSContentLister("ocs_content");
		$xml = $conl->ocs_content_list($searchstr,$sortmode,$page,$pagesize);
		$totalitems = count($xml);
		/*
		 * test page: http://localhost/v1/content/data?search=lolol
		 */
		
		if(empty($xml)){
			$txt=$this->generatexml($format,'ok',100,'');
		} else {
			$txt=$this->generatexml($format,'ok',100,'',$xml,'content','summary',2,$totalitems,$pagesize);
		}
		
		echo($txt);
		
	}




	/**	 
	 * get a list of recommendations for a content
	 * @param string $format
	 * @param string $contents
	 * @param string $searchstr
	 * @param string $searchuser
	 * @param string $sortmode
	 * @param string $page
	 * @param string $pagesize
	 * @return string xml/json
	 */
	private  function contentrecommendations($format,$contentid,$page,$pagesize) {

		$user=$this->checkpassword(false);
		$this->checktrafficlimit($user);


		$cache = new H01_CACHE('apicontentrecommendations',array($_SESSION['website'],$_SESSION['lang'],$contentid,$format));
		if ($cache->exist()) {
			$cache->get();
			unset($cache);
		} else {

			$xml=H01_CONTENT::getrecommendations($contentid,$page,$pagesize);
			$totalitems=$xml['totalitems'];
			unset($xml['totalitems']);

			$txt=$this->generatexml($format,'ok',100,'',$xml,'content','basic',2,$totalitems,$pagesize);

			$cache->put($txt);
			unset($cache);
			echo($txt);
		}

	}






	/**	 
	 * get a list of contents categories
	 * @param string $format
	 * @return string xml/json
	 */
	private  function contentcategories($format) {
		global $WEBSITECONTENT;
		global $WEBSITECONTENTTHEME;

		$user=$this->checkpassword(false);
		$this->checktrafficlimit($user);

		$i=0;
		foreach($WEBSITECONTENT as $key=>$value) {
			$i++;
			$xml[$i]['id']=$key;
			$xml[$i]['name']=$value;
		}
		$txt=$this->generatexml($format,'ok',100,'',$xml,'category','',2,count($WEBSITECONTENT));

		echo($txt);
	}

	/**	 
	 * get a list of contents licenses
	 * @param string $format
	 * @return string xml/json
	 */
	private  function contentlicenses($format) {
		global $contentlicense;
		global $contentlicenselink;

		$user=$this->checkpassword(false);
		$this->checktrafficlimit($user);

		$i=0;
		foreach($contentlicense as $key=>$value) {
			$i++;
			$xml[$i]['id']=$key;
			$xml[$i]['name']=$value;
			$xml[$i]['link']=$contentlicenselink[$key];
		}
		$txt=$this->generatexml($format,'ok',100,'',$xml,'license','',2,count($contentlicense));

		echo($txt);
	}

	/**	 
	 * get a list of contents distributions
	 * @param string $format
	 * @return string xml/json
	 */
	private  function contentdistributions($format) {
		global $DISTRIBUTIONSTYPES;

		$user=$this->checkpassword(false);
		$this->checktrafficlimit($user);

		$i=0;
		foreach($DISTRIBUTIONSTYPES as $key=>$value) {
			$i++;
			$xml[$i]['id']=$key;
			$xml[$i]['name']=$value;
		}
		$txt=$this->generatexml($format,'ok',100,'',$xml,'distribution','',2,count($DISTRIBUTIONSTYPES));

		echo($txt);
	}


	/**	 
	 * get a list of contents homepages
	 * @param string $format
	 * @return string xml/json
	 */
	private  function contenthomepages($format) {
		$user=$this->checkpassword(false);
		$this->checktrafficlimit($user);

		$i=0;
		foreach(H01_CONTENT::$LINK_CATEGORY as $key=>$value) {
			$i++;
			$xml[$i]['id']=$key;
			$xml[$i]['name']=$value;
		}
		$txt=$this->generatexml($format,'ok',100,'',$xml,'homepagetypes','',2,count(H01_CONTENT::$LINK_CATEGORY));

		echo($txt);
	}


	/**	 
	 * get a list of contents dependencies
	 * @param string $format
	 * @return string xml/json
	 */
	private  function contentdependencies($format) {
		global $DEPENDTYPES;

		$user=$this->checkpassword(false);
		$this->checktrafficlimit($user);

		$i=0;
		foreach($DEPENDTYPES as $key=>$value) {
			$i++;
			$xml[$i]['id']=$key;
			$xml[$i]['name']=$value;
		}
		$txt=$this->generatexml($format,'ok',100,'',$xml,'dependtypes','',2,count($DEPENDTYPES));

		echo($txt);
	}



	/**	 
	 * vote for a content
	 * @param string $format
	 * @param string $content
	 * @param string $vote
	 * @return string xml/json
	 */
	private  function contentvote($format,$content,$vote) {
		
		$user=$this->checkpassword(true);
		$this->checktrafficlimit($user);
		
		$con = new OCSContent();
		
		// fetch data
		$content=addslashes($content);
		$vote=addslashes($vote);
		
		// check data
		if (!$con->load($content)) {
			$txt=$this->generatexml($format,'failed',101,'content not found');
		} else {
			if($user<>'') $con->set_score($vote);
			$txt=$this->generatexml($format,'ok',100,'');
		}
		echo($txt);
	}


	/**	 
	 * delete a preview picture of a content
	 * @param string $format
	 * @param string $contentid
	 * @param string $previewid
	 * @return string xml/json
	 */
	private  function contentpreviewdelete($format,$contentid,$previewid) {
		$user=$this->checkpassword(true);
		$this->checktrafficlimit($user);
		$content=addslashes($contentid);
		$preview=addslashes($previewid);

		// fetch data
		$con = new OCSContent();

		if($con->load($content)){
			if($con->is_preview_available($previewid)){
				if($con->is_owned($this->main->user->id())) {
					
					$con->previewdelete($content,$preview);
					
					$txt=$this->generatexml($format,'ok',100,'');
				} else {
					$txt=$this->generatexml($format,'failed',101,'no permission to change content');
				}
			} else {
				$txt=$this->generatexml($format,'failed',102,'preview not found');
			}
		}
		echo($txt);
	}

	/**	 
	 * upload a preview picture of a content
	 * @param string $format
	 * @param string $contentid
	 * @param string $previewid
	 * @return string xml/json
	 */
	private  function contentpreviewupload($format,$contentid,$previewid) {
		$user=$this->checkpassword(true);
		$this->checktrafficlimit($user);
		$content=addslashes($contentid);
		$preview=addslashes($previewid);

		// fetch data
		$con = new OCSContent();

		if(($preview==1) or ($preview==2) or ($preview==3)) {

			if($con->load($content) and $con->is_owned($this->main->user->id())) {

				if(isset($_FILES['localfile']['name']) and isset($_FILES['localfile']['name']) and ($_FILES['localfile']['name']<>'' and $_FILES['localfile']['name']<>'none' and $_FILES['localfile']['tmp_name']<>'' and $_FILES['localfile']['tmp_name']<>'none')) {
					if($con->previewadd($content,'localfile',$preview)){
						$txt=$this->generatexml($format,'ok',100,'');
					} else {
						$this->main->log->error("previewadd crashed lol!");
					}
				} else {
					$txt=$this->generatexml($format,'failed',101,'localfile not found');
				}
			} else {
				$txt=$this->generatexml($format,'failed',102,'no permission to change content');
			}
		} else {
			$txt=$this->generatexml($format,'failed',103,'preview must be 1, 2 or 3');
		}
		echo($txt);
	}



	/**	 
	 * delete the downloadfile from a content
	 * @param string $format
	 * @param string $contentid
	 * @return string xml/json
	 */
	private  function contentdownloaddelete($format,$contentid) {
		$user=$this->checkpassword(true);
		$this->checktrafficlimit($user);
		$content=addslashes($contentid);

		// fetch data
		$con = new OCSContent();

		if($con->load($content) and $con->is_owned($this->main->user->id())) {

			$con->downloaddelete();
			$txt=$this->generatexml($format,'ok',100,'');
		} else {
			$txt=$this->generatexml($format,'failed',101,'no permission to change content');
		}

		echo($txt);

	}

	/**	 
	 * upload the downloadfile for a content
	 * @param string $format
	 * @param string $contentid
	 * @return string xml/json
	 */
	private  function contentdownloadupload($format,$contentid) {
		$user=$this->checkpassword(true);
		$this->checktrafficlimit($user);
		$content=addslashes($contentid);

		// fetch data
		$con = new OCSContent();

		if($con->load($content) and $con->is_owned($this->main->user->id())) {
		
			if(isset($_FILES['localfile']['name']) and isset($_FILES['localfile']['name']) and ($_FILES['localfile']['name']<>'' and $_FILES['localfile']['name']<>'none' and $_FILES['localfile']['tmp_name']<>'' and $_FILES['localfile']['tmp_name']<>'none')) {
				if($con->downloadadd($content,'localfile')){
					$txt=$this->generatexml($format,'ok',100,'');
				}else{
					$txt=$this->generatexml($format,'failed',101,$error);
				} 
			} else {
				$txt=$this->generatexml($format,'failed',102,'localfile not found');
			}
		} else {
			$txt=$this->generatexml($format,'failed',103,'no permission to change content');
		}

		echo($txt);

	}




	/**	 
	 * add a new content
	 * @param string $format
	 * @return string xml/json
	 */
	private  function contentadd($format) {
		$user=$this->checkpassword();
		$this->checktrafficlimit($user);

		if($this->main->user->is_logged()) {

			$data=array();
			$data['name']=$this->readdata('name','text');
			$data['type']=$this->readdata('type','int');
			
			if($this->readdata('downloadname1','text')<>'')			 $data['downloadname1']			=$this->readdata('downloadname1','text');
			if($this->readdata('downloadlink1','text')<>'')			 $data['downloadlink1']			=$this->readdata('downloadlink1','text');
			if($this->readdata('description','text')<>'')					$data['description']=$this->readdata('description','text');
			if($this->readdata('summary','text')<>'')							$data['summary']=$this->readdata('summary','text');
			if($this->readdata('version','text')<>'')							$data['version']=$this->readdata('version','text');
			if($this->readdata('changelog','text')<>'')						$data['changelog']=$this->readdata('changelog','text');
			
			if(($data['name']<>'') and ($data['type']<>0)) {
				$content = new OCSContent();
				$content->set_owner($this->main->user->id());
				$content->set_data($data);
				$content->save();
				
				$xml = array();
				$xml[0]['id'] = $content->id();
				$txt = $this->generatexml($format,'ok',100,'',$xml,'content','',2); 
			}else{
				$txt = $this->generatexml($format,'failed',101,'please specify all mandatory fields');
			}
		}else{
			$txt=$this->generatexml($format,'failed',102,'no permission to change content');
		}

		echo($txt);

	}



	/**	 
	 * edit a content entry
	 * @param string $format
	 * @param string $contentid
	 * @return string xml/json
	 */
	private  function contentedit($format,$contentid) {

		$user=$this->checkpassword(true);
		$this->checktrafficlimit($user);
		$content=addslashes($contentid);
		
		// fetch data
		$con = new OCSContent();
		if($con->load($content) and $this->main->user->is_logged() and $this->main->user->id() == $con->owner) {

			$data=array();
			if($this->readdata('name','text')<>'')		$data['name'] = $this->readdata('name','text');
			if($this->readdata('type','text')<>'')		$data['type'] = $this->readdata('type','text'); else $data['type'] = $con->type;
			
			if($this->readdata('downloadname1','text')<>$con->downloadname1)		$data['downloadname1'] = $this->readdata('downloadname1','text');
			if($this->readdata('downloadlink1','text')<>$con->downloadlink1)		$data['downloadlink1'] = $this->readdata('downloadlink1','text');
			if($this->readdata('description','text')<>$con->description)		$data['description'] = $this->readdata('description','text');
			if($this->readdata('summary','text')<>$con->summary)			$data['summary'] = $this->readdata('summary','text');
			if($this->readdata('version','text')<>$con->version)			$data['version'] = $this->readdata('version','text');
			if($this->readdata('changelog','text')<>$con->changelog)			$data['changelog'] = $this->readdata('changelog','text');
			
			
			if(($data['name']<>'') and ($data['type']<>0)) {
				$con->set_data($data);
				$con->update();
				
				$xml = array();
				$txt = $this->generatexml($format,'ok',100,'',$xml,'content'); 
			}else{
				$txt = $this->generatexml($format,'failed',101,'please specify all mandatory fields');
			}
		}else{
			$txt=$this->generatexml($format,'failed',102,'no permission to change content');
		}

		echo($txt);

	}



	/**	 
	 * delete a content
	 * @param string $format
	 * @param string $contentid
	 * @return string xml/json
	 */
	private  function contentdelete($format,$contentid) {
		
		$user=$this->checkpassword(true);
		$this->checktrafficlimit($user);
		$content=addslashes($contentid);
		
		// fetch data
		$con = new OCSContent();
		if(!$con->load($content)){
			$txt=$this->generatexml($format,'failed',101,'no permission to change content');
		} else {
			if(!$con->is_owned($this->main->user->id())){
				$txt=$this->generatexml($format,'failed',101,'no permission to change content');
			} else {
				$con->delete();
				$txt=$this->generatexml($format,'ok',100,'');
			}
		}
		
		echo($txt);
	}


	//KNOWLEDGEBASE API #############################################

	/**	 
	 * get a specific knowledgebase entry
	 * @param string $format
	 * @param string $kbid
	 * @return string xml/json
	 */
	private  function knowledgebaseget($format,$kbid) {
		$user=$this->checkpassword();
		$this->checktrafficlimit($user);
		$kbid=addslashes($kbid);

		$cache = new H01_CACHE('apiknowledgebaseget',array($_SESSION['website'],$_SESSION['lang'],$kbid,$format));
		if ($cache->exist()) {
			$cache->get();
			unset($cache);
		} else {

			// fetch data
			$con=H01_KNOWLEDGEBASE::getentry($kbid);

			// check data
			if (($con['id'])==0)	{
				$txt=$this->generatexml($format,'failed',101,'entry not found');
			} else {

				if(trim($con['answer'])=='') $status=1; else $status=2;
				$xml['id']=$con['id'];
				$xml['status']=H01_KNOWLEDGEBASE::$STATUS[1][$status];
				$xml['contentid']=$con['contentid'];
				$xml['category']=H01_KNOWLEDGEBASE::$TYPE[1][1][$con['type']];
				$xml['user']=$con['user'];
				$xml['changed']=date('c',$con['changed']);
				$xml['name']=$con['name'];
				$xml['description']=$con['description'];
				$xml['answeruser']=$con['user2'];
				$xml['answer']=$con['answer'];
				$xml['comments']=$con['commentscount'];
				$xml['detailpage']='http://'.CONFIG_WEBSITEHOST.'/content/show.php?action=knowledgebase&content='.$con['contentid'].'&kbid='.$con['id'];

				// preview
				if (!empty($con['pic1'])) $pic1=$con['pic1']; else $pic1='';
				if (!empty($con['pic2'])) $pic2=$con['pic2']; else $pic2='';
				if (!empty($con['pic3'])) $pic3=$con['pic3']; else $pic3='';


				if(!empty($pic1)) $xml['previewpic1']='http://'.CONFIG_WEBSITEHOST.'/CONTENT/knowledgebase-pics1/'.$pic1;
				if(!empty($pic1)) $xml['smallpreviewpic1']='http://'.CONFIG_WEBSITEHOST.'/CONTENT/knowledgebase-m1/'.$pic1;

				if(!empty($pic2)) $xml['previewpic2']='http://'.CONFIG_WEBSITEHOST.'/CONTENT/knowledgebase-pics2/'.$pic2;
				if(!empty($pic2)) $xml['smallpreviewpic2']='http://'.CONFIG_WEBSITEHOST.'/CONTENT/knowledgebase-m2/'.$pic2;

				if(!empty($pic3)) $xml['previewpic3']='http://'.CONFIG_WEBSITEHOST.'/CONTENT/knowledgebase-pics3/'.$pic3;
				if(!empty($pic3)) $xml['smallpreviewpic3']='http://'.CONFIG_WEBSITEHOST.'/CONTENT/knowledgebase-m3/'.$pic3;

				if(!empty($pic4)) $xml['previewpic4']='http://'.CONFIG_WEBSITEHOST.'/CONTENT/knowledgebase-pics4/'.$pic4;
				if(!empty($pic4)) $xml['smallpreviewpic4']='http://'.CONFIG_WEBSITEHOST.'/CONTENT/knowledgebase-m4/'.$pic4;

				$xml2[0]=$xml;
				$txt=$this->generatexml($format,'ok',100,'',$xml2,'knowledgebase','',2);

			}

			$cache->put($txt);
			unset($cache);
			echo($txt);
		}
	}


	/**	 
	 * get a list of knowledgebase entries
	 * @param string $format
	 * @param string $contents
	 * @param string $searchstr
	 * @param string $sortmode
	 * @param string $page
	 * @param string $pagesize
	 * @return string xml/json
	 */
	private  function knowledgebaselist($format,$contents,$searchstr,$sortmode,$page,$pagesize) {
		$user=$this->checkpassword();
		$this->checktrafficlimit($user);

		$cache = new H01_CACHE('apiknowledgebaselist',array($_SESSION['website'],$_SESSION['lang'],$format,$contents.$searchstr.$sortmode.$page.$pagesize));
		if ($cache->exist()) {
			$cache->get();
			unset($cache);
		} else {

			$xml=H01_KNOWLEDGEBASE::search($contents,$searchstr,$sortmode,$page,$pagesize);
			$totalitems=$xml['totalitems'];
			unset($xml['totalitems']);

			$txt=$this->generatexml($format,'ok',100,'',$xml,'content','detail',2,$totalitems,$pagesize);

			$cache->put($txt);
			unset($cache);
			echo($txt);
		}

	}



	// EVENT API #############################################

	/**	 
	 * get a specific event
	 * @param string $format
	 * @param string $evid
	 * @return string xml/json
	 */
	private  function eventget($format,$evid) {

		$user=$this->checkpassword();
		$this->checktrafficlimit($user);
		$evid=addslashes($evid);

		$cache = new H01_CACHE('apieventget',array($_SESSION['website'],$_SESSION['lang'],$evid,$format));
		if ($cache->exist()) {
			$cache->get();
			unset($cache);
		} else {

			// fetch data
			$con=H01_EVENT::get($evid,0);

			// check data
			if (($con['id'])==0)	{
				$txt=$this->generatexml($format,'failed',100,'entry not found');
			} else {

				$xml['id']=$con['id'];
				$xml['name']=$con['name'];
				$xml['description']=$con['description'];
				$xml['category']=H01_EVENT::$CATEGORIES[0][1][$con['category']];
				$xml['startdate']=date('c',$con['startdate']);
				$xml['enddate']=date('c',$con['enddate']);
				$xml['user']=$con['user'];
				$xml['organizer']=$con['organizer'];
				$xml['location']=$con['location'];
				$xml['city']=$con['city'];
				$xml['country']=H01_USER::$COUNTRIES[$con['country']];
				$xml['longitude']=$con['longitude'];
				$xml['latitude']=$con['latitude'];
				$xml['homepage']=$con['homepage'];
				$xml['tel']=$con['tel'];
				$xml['fax']=$con['fax'];
				$xml['email']=$con['email'];
				$xml['changed']=date('c',$con['changed']);
				$xml['comments']=$con['comments'];
				$xml['participants']=$con['participants'];
				$xml['detailpage']='http://'.CONFIG_WEBSITEHOST.'/events/?id='.$con['id'];

				$photourl='/CONTENT/event-badge/0/'.$con['id'].'.';
				if (file_exists(CONFIG_DOCUMENT_ROOT.$photourl.'gif')) $xml['badge']='http://'.CONFIG_WEBSITEHOST.$photourl.'gif';
				elseif (file_exists(CONFIG_DOCUMENT_ROOT.$photourl.'png')) $xml['badge']='http://'.CONFIG_WEBSITEHOST.$photourl.'png';
				elseif (file_exists(CONFIG_DOCUMENT_ROOT.$photourl.'jpg')) $xml['badge']='http://'.CONFIG_WEBSITEHOST.$photourl.'jpg';
				else $xml['badge']='';


				$photourl='/CONTENT/event-image/0/'.$con['id'].'.';
				if (file_exists(CONFIG_DOCUMENT_ROOT.$photourl.'gif')) $xml['image']='http://'.CONFIG_WEBSITEHOST.$photourl.'gif';
				elseif (file_exists(CONFIG_DOCUMENT_ROOT.$photourl.'png')) $xml['image']='http://'.CONFIG_WEBSITEHOST.$photourl.'png';
				elseif (file_exists(CONFIG_DOCUMENT_ROOT.$photourl.'jpg')) $xml['image']='http://'.CONFIG_WEBSITEHOST.$photourl.'jpg';
				else $xml['image']='';


				$xml2[0]=$xml;
				$txt=$this->generatexml($format,'ok',100,'',$xml2,'event','',2);

			}

			$cache->put($txt);
			unset($cache);
			echo($txt);
		}
	}


	/**	 
	 * get a list of events
	 * @param string $format
	 * @param string $type
	 * @param string $country
	 * @param string $startat
	 * @param string $searchstr
	 * @param string $sortmode
	 * @param string $page
	 * @param string $pagesize
	 * @return string xml/json
	 */
	private  function eventlist($format,$type,$country,$startat,$searchstr,$sortmode,$page,$pagesize) {
		$user=$this->checkpassword();
		$this->checktrafficlimit($user);

		$cache = new H01_CACHE('apieventlist',array($_SESSION['website'],$_SESSION['lang'],$format,$type.$country.$startat.$searchstr.$sortmode.$page.$pagesize));
		if ($cache->exist()) {
			$cache->get();
			unset($cache);
		} else {

			$xml=H01_EVENT::search($type,$country,$startat,$searchstr,$sortmode,$page,$pagesize);
			$totalitems=$xml['totalitems'];
			unset($xml['totalitems']);

			$txt=$this->generatexml($format,'ok',100,'',$xml,'event','detail',2,$totalitems,$pagesize);

			$cache->put($txt);
			unset($cache);
			echo($txt);
		}

	}


	/**	 
	 * add a new event
	 * @param string $format
	 * @return string xml/json
	 */
	private  function eventadd($format) {

		$user=$this->checkpassword();
		$this->checktrafficlimit($user);

		$name=$this->readdata('name','text');
		$category=$this->readdata('category','int');

		if($this->readdata('description','text')<>'')			$description=$this->readdata('description','text'); else $description='';
		if($this->readdata('startdate','text')<>'')				$startdate=strtotime($this->readdata('startdate','raw')); else $startdate=0;
		if($this->readdata('enddate','text')<>'')					$enddate=strtotime($this->readdata('enddate','raw')); else $enddate=0;

		if($this->readdata('organizer','text')<>'')				$organizer=$this->readdata('organizer','text'); else $organizer='';
		if($this->readdata('location','text')<>'')				 $location=$this->readdata('location','text'); else $location='';
		if($this->readdata('city','text')<>'')						 $city=$this->readdata('city','text'); else $city='';
		if($this->readdata('country','text')<>'')					$country=$this->readdata('country','text'); else $country='';
		$co=array_search(strtoupper($country),H01_USER::$COUNTRIESISO);

		if($this->readdata('longitude','float')<>'')			 $longitude=$this->readdata('longitude','float'); else $longitude='';
		if($this->readdata('latitude','float')<>'')				$latitude=$this->readdata('latitude','float'); else $latitude='';

		if($this->readdata('homepage','text')<>'')				 $homepage=$this->readdata('homepage','text'); else $homepage='';
		if($this->readdata('tel','text')<>'')							$tel=$this->readdata('tel','text'); else $tel='';
		if($this->readdata('fax','text')<>'')							$fax=$this->readdata('fax','text'); else $fax='';
		if($this->readdata('email','text')<>'')						$email=$this->readdata('email','text'); else $email='';

		if($user<>'') {
			if(($name<>'' and $category<>0)) {
				$id=H01_EVENT::create(CONFIG_EVENTDB,$name,$description,$category,$startdate,$enddate,$user,CONFIG_USERDB,$organizer,$location,$city,$co,$longitude,$latitude,$homepage,$tel,$fax,$email);
				$xml=array();
				$xml[0]['id']=$id;
				$txt=$this->generatexml($format,'ok',100,'',$xml,'event','',2);
			}else{
				$txt=$this->generatexml($format,'failed',101,'please specify all mandatory fields');
			}
		}else{
			$txt=$this->generatexml($format,'failed',102,'no permission to add event');
		}

		echo($txt);

	}


	/**	 
	 * delete a event
	 * @param string $format
	 * @param string $eventid
	 * @return string xml/json
	 */
	private  function eventdelete($format,$eventid) {
		$user=$this->checkpassword();
		$this->checktrafficlimit($user);
		$event=addslashes($eventid);

		// fetch data
		$con=H01_EVENT::get($event,CONFIG_EVENTDB);
		if(isset($con['user'])) {

			if((($con['user']==$user) and ($con['userdb']==CONFIG_USERDB) ) or (H01_AUTH::checkuser(PERM_Event_Admin,$user,CONFIG_USERDB))) {
				H01_EVENT::del($event,$user);
				$txt=$this->generatexml($format,'ok',100,'');
			}else{
				$txt=$this->generatexml($format,'failed',101,'no permission to change event');
			}
		}else{
			$txt=$this->generatexml($format,'failed',101,'ano permission to change event');
		}

		echo($txt);

	}


	/**	 
	 * edit a event
	 * @param string $format
	 * @param string $eventid
	 * @return string xml/json
	 */
	private  function eventedit($format,$eventid) {
		$user=$this->checkpassword();
		$this->checktrafficlimit($user);
		$event=addslashes($eventid);

		// fetch data
		$DBevent=H01_EVENT::get($event,CONFIG_EVENTDB);
		if(isset($DBevent['user'])) {

			if((($DBevent['user']==$user) and ($DBevent['userdb']==CONFIG_USERDB) ) or (H01_AUTH::checkuser(PERM_Event_Admin,$user,CONFIG_USERDB))) {

				if(isset($_POST['name']))						 $name=$this->readdata('name','text');											 else $name=$DBevent['name'];
				if(isset($_POST['category']))				 $category=$this->readdata('category','int');								else $category=$DBevent['category'];

				if(isset($_POST['description']))			$description=$this->readdata('description','text');				 else $description=$DBevent['description'];
				if(isset($_POST['startdate']))				$startdate=strtotime($this->readdata('startdate','raw'));	 else $startdate=$DBevent['startdate'];
				if(isset($_POST['enddate']))					$enddate=strtotime($this->readdata('enddate','raw'));			 else $enddate=$DBevent['enddate'];
				if(isset($_POST['organizer']))				$organizer=$this->readdata('organizer','text');						 else $organizer=$DBevent['organizer'];
				if(isset($_POST['location']))				 $location=$this->readdata('location','text');							 else $location=$DBevent['location'];
				if(isset($_POST['city']))						 $city=$this->readdata('city','text');											 else $city=$DBevent['city'];
				if(isset($_POST['country'])) {
					$country=$this->readdata('country','text');	 
					$country=array_search(strtoupper($country),H01_USER::$COUNTRIESISO);
				}else {
					$country=$DBevent['country'];
				}
				if(isset($_POST['longitude']))				$longitude=$this->readdata('longitude','float');						else $longitude=$DBevent['longitude'];
				if(isset($_POST['latitude']))				 $latitude=$this->readdata('latitude','float');							else $latitude=$DBevent['latitude'];
				if(isset($_POST['homepage']))				 $homepage=$this->readdata('homepage','text');							 else $homepage=$DBevent['homepage'];
				if(isset($_POST['tel']))							$tel=$this->readdata('tel','text');												 else $tel=$DBevent['tel'];
				if(isset($_POST['fax']))							$fax=$this->readdata('fax','text');												 else $fax=$DBevent['fax'];
				if(isset($_POST['email']))						$email=$this->readdata('email','text');										 else $email=$DBevent['email'];

				if(($name<>'') and ($category<>0)) {

					H01_EVENT::edit($event,CONFIG_EVENTDB,$name,$description,$category,$startdate,$enddate,$user,CONFIG_USERDB,$organizer,$location,$city,$country,$longitude,$latitude,$homepage,$tel,$fax,$email);
					$txt=$this->generatexml($format,'ok',100,'');
				}else{
					$txt=$this->generatexml($format,'failed',101,'please specify all mandatory fields');
				}
			}else{
				$txt=$this->generatexml($format,'failed',102,'no permission to change event');
			}
		}else{
			$txt=$this->generatexml($format,'failed',102,'event not found');
		}

		echo($txt);
	}



	// COMMENTS API #############################################

	/**	 
	 * add a comment
	 * @param string $format
	 * @param string $content
	 * @param string $parent
	 * @param string $subject
	 * @param string $message
	 * @return string xml/json
	 */
	private function commentsadd($format,$type,$content,$content2,$parent,$subject,$message) {
		$user = $this->checkpassword(true);
		$this->checktrafficlimit($user);
		$data['parent'] = strip_tags(addslashes($parent));
		$data['subject'] = strip_tags(addslashes($subject));
		$data['message'] = strip_tags(addslashes($message));
		$data['content'] = strip_tags(addslashes($content));
		$data['content2'] = strip_tags(addslashes($content2));
		$data['type'] = strip_tags(addslashes($type));
		$data['owner'] = $this->main->user->id();

	 //types
	 // just 1 is accepted
	 // 1 - content
		
		//setting content type as default
		if(!in_array($data['type'],array(1,4,7,8))) $data['type']=1;
		
		if($user<>'') {
			if($data['message']<>'' and $data['subject']<>'') {
				if($data['content']<>0) {
					$comment = new OCSComment(); //creating new object
					$comment->set_data($data); //loading new data for comment
					$comment->save_to_db();
					$id = $comment->id();
					$xml[0]['id'] = $id;
					echo($this->generatexml($format,'ok',100,'',$xml,'comment','',2));
				} else {
					echo($this->generatexml($format,'failed',101,'content must not be empty'));
				}
			} else {
				echo($this->generatexml($format,'failed',102,'message or subject must not be empty'));
			}
		} else {
			echo($this->generatexml($format,'failed',103,'no permission to add a comment'));
		}

	}



	private  function commentsget($format,$type,$content,$content2,$page,$pagesize) {
		$user=$this->checkpassword(false);
		$this->checktrafficlimit($user);
		$type = strip_tags(addslashes($type));
		$content = strip_tags(addslashes($content));
		$content2 = strip_tags(addslashes($content2));
		$page = strip_tags(addslashes($page));
		$pagesize = strip_tags(addslashes($pagesize));

	 //types
	 // 1 - content
	 // 4 - forum
	 // 7 - knowledgebase
	 // 8 - event

		if(!in_array($type,array(1,4,7,8))) $type=1;
		
		$coml = new OCSCommentLister();
		$comments = $coml->ocs_comment_list($type,$content,$content2,$page,$pagesize);
		$totalitems = count($comments);
//			$txt=$this->generatexml($format,'ok',100,'',$xml,'event','detail',2,$totalitems,$pagesize);

		$txt=$this->generatexml($format,'ok',100,'',$comments,'comment','','dynamic',$totalitems,$pagesize);
		echo($txt);


	}


	/**	 
	 * vote for a comment
	 * @param string $format
	 * @param string $id
	 * @param string $score
	 * @return string xml/json
	 */
	private  function commentvote($format,$id,$score) {
		$user=$this->checkpassword(true);
		$this->checktrafficlimit($user);
		
		$comment = new OCSComment();
		if($comment->load($id)){
			
			$comment->set_score($score);
			$txt=$this->generatexml($format,'ok',100,'');
			echo($txt);
		} else {
			$txt=$this->generatexml($format,'failed',101,'comment not found');
		}
	}


	// FORUM
	
	/**
	 * Get a list of forums
	 * @param string	$format
	 * @param int		 $page			The list page. You can control the size of a page with the pagesize argument. The first page is 0, the second is 1.
	 * @param int		 $pagesize	The amount of entries per page.
	 * @return	string	xml/json
	 */
	private  function forumlist($format,$page,$pagesize){
		$user=$this->checkpassword();
		$this->checktrafficlimit($user);
		
		// Call forum implementation here
		$txt=$this->generatexml($format,'ok',100,'');
		echo($txt);
	}

	/**
	 * Gets a list of a specific set of topics.
	 * @param string	$format
	 * @param string	$forum				Id of the forum you are requesting a list of. Not required if a search term is provided.
	 * @param string	$search			 a keyword you want find in the name.
	 * @param string	$description	the description or comment of a topic. Not required if a forum id is provided.
	 * @param string	$sortmode		 The sortmode of the list. Possible values are: "new" - newest first or "alpha" - alphabetical
	 * @param int		 $page				 The list page. You can control the size of a page with the pagesize argument. The first page is 0, the second is 1.
	 * @param int		 $pagesize		 The amount of entries per page.
	 * @return string xml/json
	 */
	private  function forumtopiclist($format,$forum,$search,$description,$sortmode,$page,$pagesize){
		$user=$this->checkpassword();
		$this->checktrafficlimit($user);
		
		// Call forum implementation here
		$txt=$this->generatexml($format,'ok',100,'');
		echo($txt);
	}
	
	/**
	 * Add a new topic to a forum. Only authenticated users are allowed to access this method.
	 * Authentication is done by sending a Basic HTTP Authorisation header. All arguments are
	 * mandatory.
	 * @param string	$format
	 * @param string	$subject	Subject of the new topic
	 * @param string	$content	Content of the first post of the new topic
	 * @param string	$forum		id of the forum entry to be added to if available
	 * @return string xml/json
	 */
	private  function forumtopicadd($format,$subject,$content,$forum){
		$user=$this->checkpassword();
		$this->checktrafficlimit($user);
		
		// Call forum implementation here
		$txt=$this->generatexml($format,'ok',100,'');
		echo($txt);
	}

	// BUILDSERVICE

	/**
	 * Create a new project in the build service
	 * @param string $format
	 * @param string $name
	 * @param string $version
	 * @param string $license
	 * @param string $url
	 * @param array	$developers
	 * @param string $summary
	 * @param string $description
	 * @param string $requirements
	 * @param string $specfile
	 * @return string xml/json
	 */
	private  function buildserviceprojectcreate($format,$name='',$version='',$license='',$url='',$developers='',$summary='',$description='',$requirements='',$specfile=''){
		$user=$this->checkpassword(true);
		$this->checktrafficlimit($user);
		
		if(strlen($name)<1){
			echo($this->generatexml($format,'failed',101,'required argument missing: name'));
			return;
		}
		
		$data=H01_BUILDSERVICE::projectcreate($user,CONFIG_USERDB,$name,$version,$license,$url,$developers,$summary,$description,$requirements,$specfile);
		$txt="";
		if($data!=NULL) {
			$txt=$this->generatexml($format,'ok',100,'',$data,'buildservice','','dynamic');
			// This looks a bit odd - but errors are also cached, and as such we got to expire the error
			// page for attempting to fetch a wrongly IDd project
			H01_CACHEADMIN::cleancache('apibuildserviceprojectget',$_SESSION['website'],$_SESSION['lang'],$format,$user.'#'.$data['projectid']);
			H01_CACHEADMIN::cleancache('apibuildserviceprojectlist',$_SESSION['website'],$_SESSION['lang'],$format,$user);
		} else
			$txt=$this->generatexml($format,'failed',101,'');
		echo($txt);
	}
	
	/**
	 * Get the data for a project in the build service
	 * @param string $format
	 * @param int $projectID
	 * @return string xml/json
	 */
	private  function buildserviceprojectget($format,$projectID){
		$user=$this->checkpassword(true);
		$this->checktrafficlimit($user);
	
		$cache = new H01_CACHE('apibuildserviceprojectget',array($_SESSION['website'],$_SESSION['lang'],$format,$user.'#'.$projectID));
		if ($cache->exist()) {
			$cache->get();
			unset($cache);
		} else {
			$txt="";
			$data=H01_BUILDSERVICE::projectget($user,CONFIG_USERDB,$projectID);

			if(count($data["project"])>0)
				$txt=$this->generatexml($format,'ok',100,'',$data,'buildservice','','dynamic');
			else {
				if(is_numeric($projectID))
					$txt=$this->generatexml($format,'failed',101,'no such project');
				else
					$txt=$this->generatexml($format,'failed',102,'project id should be an integer');
			}
			$cache->put($txt);
			unset($cache);
			echo($txt);
		}
	}

	/**
	 * Delete a project in the build service
	 * @param string $format
	 * @param int $projectID
	 * @return string xml/json
	 */
	private  function buildserviceprojectdelete($format,$projectID){
		$user=$this->checkpassword(true);
		$this->checktrafficlimit($user);
		
		$data=H01_BUILDSERVICE::projectdelete($user,CONFIG_USERDB,$projectID);
		
		$txt="";
		if($data==true) {
			$txt=$this->generatexml($format,'ok',100,'');
			H01_CACHEADMIN::cleancache('apibuildserviceprojectget',$_SESSION['website'],$_SESSION['lang'],$format,$user.'#'.$projectID);
			H01_CACHEADMIN::cleancache('apibuildserviceprojectlist',$_SESSION['website'],$_SESSION['lang'],$format,$user);
		} else {
			if(is_numeric($projectID))
				$txt=$this->generatexml($format,'failed',101,'no such project');
			else
				$txt=$this->generatexml($format,'failed',102,'project id should be an integer');
		}
		
		echo($txt);
	}
	
	/**
	 * Change the details of a project in the build service
	 * @param string $format
	 * @param int		@projectID
	 * @param string $name
	 * @param string $version
	 * @param string $license
	 * @param string $url
	 * @param array	$developers
	 * @param string $summary
	 * @param string $description
	 * @param string $requirements
	 * @param string $specfile
	 * @return string xml/json
	 */
	private  function buildserviceprojectedit($format,$projectID,$name="",$version="",$license="",$url="",$developers='',$summary="",$description="",$requirements="",$specfile=""){
		$user=$this->checkpassword(true);
		$this->checktrafficlimit($user);
		
		// This looks slightly odd - we do this because the function in the buildservice module requires
		// a 0 here if you do not intend to clear the field - it checks the data type to be a real int.
		if(!array_key_exists("specfile",$_POST))
			$specfile=0;

		$data=H01_BUILDSERVICE::projectedit($user,CONFIG_USERDB,$projectID,$name,$version,$license,$url,$developers,$summary,$description,$requirements,$specfile);
		$txt="";
		if($data===true) {
			$txt=$this->generatexml($format,'ok',100,'');
			H01_CACHEADMIN::cleancache('apibuildserviceprojectget',$_SESSION['website'],$_SESSION['lang'],$format,$user.'#'.$projectID);
			H01_CACHEADMIN::cleancache('apibuildserviceprojectlist',$_SESSION['website'],$_SESSION['lang'],$format,$user);
		} else {
			if(is_numeric($projectID))
				$txt=$this->generatexml($format,'failed',101,'no such project');
			else
				$txt=$this->generatexml($format,'failed',102,'project id should be an integer');
		}
		
		echo($txt);
	}

	/**
	 * List all the projects in the build service owned by the authorized user
	 * @param string $format
	 * @param int		$page
	 * @param int		$pagesize
	 * @return string xml/json
	 */
	private  function buildserviceprojectlist($format,$page,$pagesize){
		$user=$this->checkpassword(true);
		$this->checktrafficlimit($user);
		
		$cache = new H01_CACHE('apibuildserviceprojectlist',array($_SESSION['website'],$_SESSION['lang'],$format,$user));
		if ($cache->exist()) {
			$cache->get();
			unset($cache);
		} else {
			$data=H01_BUILDSERVICE::projectlist($user,CONFIG_USERDB);
			$txt=$this->generatexml($format,'ok',100,'',$data,'project','','dynamic');
			
			$cache->put($txt);
			unset($cache);
			echo($txt);
		}
	}

	/**
	 * Upload a new source bundle (a compressed file in .zip, .tar.gz or .tar.bz2 format) containing
	 * the source code of the project
	 * @param string $format
	 * @param int		$projectID
	 * @return string xml/json
	 */
	private  function buildserviceprojectuploadsource($format,$projectID){
		$user=$this->checkpassword(true);
		$this->checktrafficlimit($user);
		
		if(!is_numeric($projectID)){
			$txt=$this->generatexml($format,'failed',102,'project id should be an integer');
		}else{
			$error=H01_BUILDSERVICE::projectuploadsource($user,CONFIG_USERDB,$projectID);

			if($error==''){
				$txt=$this->generatexml($format,'ok',100,'');
			}else{
				$txt=$this->generatexml($format,'failed',103,$error);
			}

		}

		
		echo($txt);
	}
	
	// REMOTEACCOUNTS section
	
	/**
	 * List all accounts for the currently authorised user
	 * @param string $format
	 * @return string xml/json
	 */
	private  function buildserviceremoteaccountslist($format,$page,$pagesize) {
		$user=$this->checkpassword(true);
		$this->checktrafficlimit($user);
		
		$data=H01_BUILDSERVICE::remoteaccountslist($user,CONFIG_USERDB);
		$txt=$this->generatexml($format,'ok',100,'',$data,'remoteaccount','','dynamic');
		echo($txt);
	}
	
	/**
	 * Add a remote account entry for the currently authorised user
	 * @param string $format
	 * @param int		 $type The type of account (1 == build service, 2 == publisher)
	 * @param string	$typeid The ID of the service the account pertains to
	 * @param string	$data The data to enter into the data section (any arbitrary string data)
	 * @param string	$login The user's login on the remote service
	 * @param string	$password The user's password on the remote service
	 * @return string xml/json
	 */
	private  function buildserviceremoteaccountsadd($format,$type,$typeid,$data,$login,$password) {
		$user=$this->checkpassword(true);
		$this->checktrafficlimit($user);
		
		$txt='';
		$data=H01_BUILDSERVICE::remoteaccountsadd($user,CONFIG_USERDB,$type,$typeid,$data,$login,$password);
		if(array_key_exists('remoteaccountid',$data)) {
			$txt=$this->generatexml($format,'ok',100,'');
		} else {
			$txt=$this->generatexml($format,'failed',$data['code'],$data['message']);
		}
		
		echo($txt);
	}
	
	/**
	 * Edit the specified remote account entry
	 * @param string $format
	 * @param int		 $id The ID of the remote account to edit
	 * @param string	$data The data to enter into the data section (any arbitrary string data)
	 * @param string	$login The user's login on the remote service
	 * @param string	$password The user's password on the remote service
	 * @return string xml/json
	 */
	private  function buildserviceremoteaccountsedit($format,$id,$login,$password,$data) {
		$user=$this->checkpassword(true);
		$this->checktrafficlimit($user);
		
		$txt='';
		$data=H01_BUILDSERVICE::remoteaccountsedit($user,CONFIG_USERDB,$id,$login,$password,$data);
		if($data) {
			$txt=$this->generatexml($format,'ok',100,'');
		} else {
			$txt=$this->generatexml($format,'failed',101,'no such remote account');
		}
		
		echo($txt);
	}
	
	/**
	 * Fetch all known information about a specified remote account
	 * @param string $format
	 * @param int		 $id The ID of the remote account to get
	 * @return string xml/json
	 */
	private  function buildserviceremoteaccountsget($format,$id) {
		$user=$this->checkpassword(true);
		$this->checktrafficlimit($user);
		
		$txt='';
		$data=H01_BUILDSERVICE::remoteaccountsget($user,CONFIG_USERDB,$id);
		if(!array_key_exists('code',$data)) {
			$txt=$this->generatexml($format,'ok',100,'',$data,'remoteaccount','','dynamic');
		} else {
			$txt=$this->generatexml($format,'failed',$data['code'],$data['message']);
		}
		
		echo($txt);
	}
	
	/**
	 * Delete the specified remote account entry
	 * @param string $format
	 * @param int		 $id The ID of the remote account to remove
	 * @return string xml/json
	 */
	private  function buildserviceremoteaccountsremove($format,$id) {
		$user=$this->checkpassword(true);
		$this->checktrafficlimit($user);
		
		$txt='';
		$data=H01_BUILDSERVICE::remoteaccountsremove($user,CONFIG_USERDB,$id);
		if(!is_array($data)) {
			$txt=$this->generatexml($format,'ok',100,'');
		} else {
			$txt=$this->generatexml($format,'failed',$data['code'],$data['message']);
		}
		
		echo($txt);
	}

	// BUILDSERVICES section

	/**
	 * get build service listing
	 * @param string	$format
	 * @return string xml/json
	 */
	private  function buildservicebuildserviceslist($format,$page,$pagesize) {
		$user=$this->checkpassword(false);
		$this->checktrafficlimit($user);

		$data=H01_BUILDSERVICE::buildserviceslist($user,CONFIG_USERDB);
		$txt=$this->generatexml($format,'ok',100,'',$data,array('','buildservice','','target'),'','dynamic');
		echo($txt);
	}
	
	/**
	 * get build service data
	 * @param string	$format
	 * @param string	$buildserviceID
	 * @return string xml/json
	 */
	private  function buildservicebuildservicesget($format,$buildserviceID) {
		$user=$this->checkpassword(false);
		$this->checktrafficlimit($user);

		$data=H01_BUILDSERVICE::buildservicesget($user,CONFIG_USERDB,$buildserviceID);
		if(is_array($data['buildservice']) && count($data['buildservice'])>0) {
			$txt=$this->generatexml($format,'ok',100,'',$data,array('buildservice','','target'),'','dynamic');
		} else {
			if(is_numeric($buildserviceID)) {
				$txt=$this->generatexml($format,'failed',101,'no such build service');
			} else {
				$txt=$this->generatexml($format,'failed',101,'no such build service - the build service ID should be an integer');
			}
		}
		echo($txt);
	}

	// JOBS section
	
	/**
	 * Get a list of jobs pertaining to one project on the build service
	 * @param string	$format
	 * @param int		 $projectID
	 * @param int		 $page
	 * @param int		 $pagesize
	 * @return string xml/json
	 */
	private  function buildservicejobslist($format,$projectID,$page,$pagesize){
		$user=$this->checkpassword(true);
		$this->checktrafficlimit($user);
		
		$data=H01_BUILDSERVICE::jobslist($user,CONFIG_USERDB,$projectID);
		if(!array_key_exists('code',$data)) {
			$txt=$this->generatexml($format,'ok',100,'',$data,'buildjob','','dynamic');
		} else {
			$txt=$this->generatexml($format,'failed',$data['code'],$data['message']);
		}
		
		echo($txt);
	}
	
	/**
	 * Create a new build job for a specified project, on a specified build service, with a specified
	 * target
	 * @param string	$format
	 * @param int		 $projectID
	 * @param int		 $buildserviceID
	 * @param string	$target
	 * @return string xml/json
	 */
	private  function buildservicejobscreate($format,$projectID,$buildserviceID,$target){
		$user=$this->checkpassword(true);
		$this->checktrafficlimit($user);
		
		$data=H01_BUILDSERVICE::jobscreate($projectID,$buildserviceID,$target,$user,CONFIG_USERDB);
		$txt="";
		if(array_key_exists('buildjobid',$data) && $data['buildjobid']!=NULL)
			$txt=$this->generatexml($format,'ok',100,'',$data,'buildservice','','dynamic');
		else{
			if(is_array($data) and array_key_exists('code',$data)){
				$txt=$this->generatexml($format,'failed',$data['code'],$data['message']);
			}else
				$txt=$this->generatexml($format,'failed',102,'project id should be an integer');
		}
		
		echo($txt);
	}
	
	/**
	 * Cancel a specified build job
	 * @param string	$format
	 * @param int		 $buildjobID
	 * @return string xml/json
	 */
	private  function buildservicejobscancel($format,$buildjobID){
		$user=$this->checkpassword(true);
		$this->checktrafficlimit($user);
		
		$data=H01_BUILDSERVICE::jobscancel($buildjobID,$user,CONFIG_USERDB);
		$txt="";
		if($data===true)
			$txt=$this->generatexml($format,'ok',100,'');
		else{
			if(is_numeric($buildjobID))
				$txt=$this->generatexml($format,'failed',101,'no such build job');
			else
				$txt=$this->generatexml($format,'failed',102,'build job id should be an integer');
		}
			
		echo($txt);
	}
	
	/**
	 * Get information about a specified build job
	 * @param string	$format
	 * @param int		 $buildjobID
	 * @return string xml/json
	 */
	private  function buildservicejobsget($format,$buildjobID){
		$user=$this->checkpassword(true);
		$this->checktrafficlimit($user);
		
		$data=H01_BUILDSERVICE::jobsget($buildjobID,$user,CONFIG_USERDB);
		$txt="";
		if(count($data["buildjob"])>0)
			$txt=$this->generatexml($format,'ok',100,'',$data,'buildservice','','dynamic');
		else{
			if(is_numeric($buildjobID))
				$txt=$this->generatexml($format,'failed',101,'no such build job');
			else
				$txt=$this->generatexml($format,'failed',102,'build job id should be an integer');
		}
		
		echo($txt);
	}
	
	/**
	 * Get the command output from a specified build job
	 * @param string	$format
	 * @param int		 $buildjobID
	 * @return string xml/json
	 */
	private  function buildservicejobsgetoutput($format,$buildjobID){
		$user=$this->checkpassword(true);
		$this->checktrafficlimit($user);
		
		$data=H01_BUILDSERVICE::jobsgetoutput($buildjobID,$user,CONFIG_USERDB);
		$txt="";
		if($data["output"]!==NULL)
			$txt=$this->generatexml($format,'ok',100,'',$data,'buildservice','','dynamic');
		else{
			if(is_numeric($buildjobID))
				$txt=$this->generatexml($format,'failed',101,'no such build job');
			else
				$txt=$this->generatexml($format,'failed',102,'build job id should be an integer');
		}
		
		echo($txt);
	}

	// Publishing
	
	/**
	 * Get a list of supported publishers, optionally for the currently authorised user
	 * @param string	$format
	 * @param int		 $page
	 * @param int		 $pagesize
	 * @return string xml/json
	 */
	private  function buildservicepublishinggetpublishingcapabilities($format,$page,$pagesize){
		$user=$this->checkpassword(false);
		$this->checktrafficlimit($user);
		
		$data=H01_BUILDSERVICE::publishinggetpublishingcapabilities($user,CONFIG_USERDB);
		$txt="";
		if(count($data["publishers"])>0){
			$txt=$this->generatexml($format,'ok',100,'',$data,array('','publisher','',array(3=>'field',4=>'target'),'','option'),'','dynamic');
		}else{
			if($user=='')
				$txt=$this->generatexml($format,'failed',101,'no such user');
			else
				$txt=$this->generatexml($format,'failed',102,'user has not registered with any publishers');
		}
		
		echo($txt);
	}
	
	/**
	 * Get information on a specified publisher
	 * @param string	$format
	 * @param int		 $publisherID
	 * @return string xml/json
	 */
	private  function buildservicepublishinggetpublisher($format,$publisherID){
		$user=$this->checkpassword(false);
		$this->checktrafficlimit($user);
		
		$data=H01_BUILDSERVICE::publishinggetpublisher($publisherID);
		$txt="";
		if(count($data["publisher"])>0)
			$txt=$this->generatexml($format,'ok',100,'',$data,array('','',array(3=>'field',4=>'target'),'','option'),'','dynamic');
		else{
			if(is_numeric($publisherID))
				$txt=$this->generatexml($format,'failed',101,'no such publisher');
			else
				$txt=$this->generatexml($format,'failed',102,'publisher id should be an integer');
		}
		
		echo($txt);
	}
	
	/**
	 * Publish the result of a bulid job on some specified project to a publisher
	 * @param string	$format
	 * @param int		 $buildjobID
	 * @param int		 $publisherID
	 * @return string xml/json
	 */
	private  function buildservicepublishingpublishtargetresult($format,$buildjobID,$publisherID){
		$user=$this->checkpassword(true);
		$this->checktrafficlimit($user);
		
		$data=H01_BUILDSERVICE::publishingpublishtargetresult($buildjobID,$publisherID,$user,CONFIG_USERDB);
		$txt="";
		if($data===true)
			$txt=$this->generatexml($format,'ok',100,'');
		else {
			if(is_array($data)) {
				$txt=$this->generatexml($format,'failed',$data['code'],$data['message']);
			} else if(is_numeric($buildjobID)) {
				if(is_numeric($publisherID)) {
					$txt=$this->generatexml($format,'failed',108,'publishing failed');
				} else {
					$txt=$this->generatexml($format,'failed',107,'publisher id should be an integer');
				}
			} else {
				$txt=$this->generatexml($format,'failed',105,'build job id should be an integer');
			}
		}
		
		echo($txt);
	}
	
	/**
	 * Save some field data (as connected to publishing the project) into that project
	 * @param string	$format
	 * @param int		 $projectID
	 * @param array	 $fields A bunch of field data, in the form
	 *								array( array("name"=>value,"fieldtype"=>value,"data"=>value), array(...))
	 * @return string xml/json
	 */
	private  function buildservicepublishingsavefields($format,$projectID,$fields){
		$user=$this->checkpassword(true);
		$this->checktrafficlimit($user);
		
		$data=H01_BUILDSERVICE::publishingsavefields($projectID,$fields,$user,CONFIG_USERDB);
		$txt="";
		if($data===true)
			$txt=$this->generatexml($format,'ok',100,'');
		else {
			if(is_numeric($projectID))
				$txt=$this->generatexml($format,'failed',101,'no such project');
			else
				$txt=$this->generatexml($format,'failed',102,'project id should be an integer');
		}
		
		echo($txt);
	}
	
	/**
	 * Get all the saved fields for some specified project
	 * @param string	$format
	 * @param int		 $projectID
	 * @return string xml/json
	 */
	private  function buildservicepublishinggetfields($format,$projectID){
		$user=$this->checkpassword(true);
		$this->checktrafficlimit($user);
		
		$data=H01_BUILDSERVICE::publishinggetfields($projectID,$user,CONFIG_USERDB);
		$txt="";
		if(!array_key_exists('code',$data))
			$txt=$this->generatexml($format,'ok',100,'',$data,'field','','dynamic');
		else {
			$txt=$this->generatexml($format,'failed',$data['code'],$data['message']);
		}
		
		echo($txt);
	}
}
// Little hack to get kdevelop to pick up the functions...
//include_once("../buildservice/lib_buildservice.php");

?>
