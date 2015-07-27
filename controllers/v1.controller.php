<?php
/*
 * on this file gfx inclusion is useless as gfx is already running
 */

class V1Controller extends EController
{
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
	
	public function handle() {
		/*
		// overwrite the 404 error page returncode
		header("HTTP/1.0 200 OK");
		*/

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
		$url= ERewriter::oldurl();
		
		//erasing get params
		$url = explode('?',$url)[0];
				
		if(substr($url,(strlen($url)-1))<>'/') $url.='/';	
		//$ex=str_replace('?', '/?', $url, $uno);
		$ex=explode('/',$url);
		
		//var_dump($ex);
		
		// eventhandler
		if(count($ex)==2){
			H01_GUI::showtemplate('apidoc');


		// CONFIG
		// apiconfig - GET - CONFIG
		}elseif(($method=='get') and (strtolower($ex[1])=='v1') and (strtolower($ex[2])=='config') and (count($ex)==4)){
			$format=$this->readdata('format','text');
			$this->config($format);


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

		// personedit - POST - PERSON/EDIT
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
			$contentid = addslashes($ex[4]);
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
			echo(OCSXML::generatexml($format,'failed',999,$txt));
		}
		exit();
	}
	
	private  function _checkpassword($forceuser=true) {
			//valid user account ?
			if(isset($_SERVER['PHP_AUTH_USER'])) $authuser=$_SERVER['PHP_AUTH_USER']; else $authuser='';
			if(isset($_SERVER['PHP_AUTH_PW']))	 $authpw=$_SERVER['PHP_AUTH_PW']; else $authpw='';
			
			//this small (and dirty) hack checks if the client who requested the page is konqueror
			//which is also Qt itself
			//TODO: maybe fix this thing?
			if(isset($_SERVER['HTTP_USER_AGENT'])){
				$iskonqueror = stristr($_SERVER['HTTP_USER_AGENT'],"Konqueror");
			} else {
				$iskonqueror = false;
			}
			
			if(empty($authuser)) {
				if($forceuser){
					if(!$iskonqueror){
						header("WWW-Authenticate: Basic realm=\"Private Area\"");
						header('HTTP/1.0 401 Unauthorized');
						exit;
					} else {
						$txt=OCSXML::generatexml('','failed',999,'needs authentication');
						echo($txt);
						exit;
					}
				}else{
					$identifieduser='';
				}
			}else{
				/*
				$user=H01_USER::finduserbyapikey($authuser,CONFIG_USERDB);
				if($user==false) {
				*/
					$user=OCSUser::server_checklogin($authuser,$authpw);
					if($user==false) {
						if($forceuser){
							if(!$iskonqueror){
								header("WWW-Authenticate: Basic realm=\"Private Area\"");
								header('HTTP/1.0 401 Unauthorized');
								exit;
							} else {
								$txt=OCSXML::generatexml('','failed',999,'needs authentication');
								echo($txt);
								exit;
							}
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
		return $identifieduser;
	}
	
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////                               OTHER COMPONENTS                                            ///////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	public function index()
	{
		$v1_config_url = EPageProperties::get_current_website_url()."/v1/config";
		
		echo "Hello! This webserver runs an Open Collaboration Services server.<br>";
		echo "Check <a href=\"$v1_config_url\">$v1_config_url</a> for configuring your OCS client.";
	}
	
    public function config()
    {
		$xml['version']=EConfig::$data["ocsserver"]["version"];
		$xml['website']=EConfig::$data["ocsserver"]["website"];
		$xml['host']=EConfig::$data["ocsserver"]["host"];
		$xml['contact']=EConfig::$data["ocsserver"]["contact"];
		if(EConfig::$data["ocsserver"]["ssl"]=='yes'){ $xml['ssl']='true'; } else { $xml['ssl']='false'; }
		echo(OCSXML::generatexml('xml','ok',100,'',$xml,'config','',1));
    }
    
    private  function getdebugoutput() {
		$txt='';
		$txt.="debug output:\n";
		if(isset($_SERVER['REQUEST_METHOD'])) $txt.='http request method: '.$_SERVER['REQUEST_METHOD']."\n";
		if(isset($_SERVER['REQUEST_URI'])) $txt.='http request uri: '.$_SERVER['REQUEST_URI']."\n";
		if(isset($_GET)) foreach($_GET as $key=>$value) $txt.='get parameter: '.$key.'->'.$value."\n";
		if(isset($_POST)) foreach($_POST as $key=>$value) $txt.='post parameter: '.$key.'->'.$value."\n";
		return($txt);
	}
    
    public function personcheck($format, $login, $password){
		//$user=$this->_checkpassword(false);
		////$this->checktrafficlimit($user);
		//OCSUser::server_load();
		
		if($login<>''){
			$reallogin=OCSUser::server_checklogin($login,$password); // $login,CONFIG_USERDB,$passwd,PERM_Login
			if($reallogin<>false){
				$xml['person']['personid']=$reallogin;
				echo(OCSXML::generatexml(EConfig::$data["ocsserver"]["format"],'ok',100,'',$xml,'person','check',2)); 
			}else{
					echo(OCSXML::generatexml(EConfig::$data["ocsserver"]["format"],'failed',102,'login not valid'));
			}
		}else{
			echo(OCSXML::generatexml(EConfig::$data["ocsserver"]["format"],'failed',101,'please specify all mandatory fields'));
		}
	}
    
    public function personadd($format,$login,$passwd,$firstname,$lastname,$email)
    {
		if($login<>'' and $passwd<>'' and $firstname<>'' and $lastname<>'' and $email<>''){
			if(OCSUser::isvalidpassword($passwd)){
				if(OCSUser::isloginname($login)){
					if(!OCSUser::server_exists($login)){
						if(OCSUser::server_countusersbyemail($email)==0) {
							if(OCSUser::isvalidemail($email)) {
								OCSUser::server_register($login,$passwd,$firstname,$lastname,$email);
								echo(OCSXML::generatexml(EConfig::$data["ocsserver"]["format"],'ok',100,''));
							}else{
								echo(OCSXML::generatexml(EConfig::$data["ocsserver"]["format"],'failed',106,'email already taken'));
							}
						}else{
							echo(OCSXML::generatexml(EConfig::$data["ocsserver"]["format"],'failed',105,'email invalid'));
						}
					}else{
						echo(OCSXML::generatexml(EConfig::$data["ocsserver"]["format"],'failed',104,'login already exists'));
					}
				}else{
					echo(OCSXML::generatexml(EConfig::$data["ocsserver"]["format"],'failed',103,'please specify a valid login'));
				}
			}else{
				echo(OCSXML::generatexml(EConfig::$data["ocsserver"]["format"],'failed',102,'please specify a valid password'));
			}
		}else{
			echo(OCSXML::generatexml(EConfig::$data["ocsserver"]["format"],'failed',101,'please specify all mandatory fields'));
		}
	}
    
    public function personget($format,$username="")
    {
		if(empty($username)){
			$user=$this->_checkpassword();
			
			$username=$user;
			
			$DBuser = OCSUser::server_get_user_info($username);
			
			if($DBuser==false){
				$txt=OCSXML::generatexml(EConfig::$data["ocsserver"]["format"],'failed',101,'person not found');
				echo($txt);
			}else{
				if(isset($DBuser[0]) and is_array($DBuser[0])){
					$DBuser = $DBuser[0];
				}
				$xml=array();
				$xml[0]['personid']=$DBuser['login'];
				$xml[0]['firstname']=$DBuser['firstname'];
				$xml[0]['lastname']=$DBuser['lastname'];
				$xml[0]['email']=$DBuser['email'];
				
				//ELog::pd($xml);
				//$xml[0]['description']=H01_UTIL::bbcode2html($DBuser['description']);
				
				$txt=OCSXML::generatexml(EConfig::$data["ocsserver"]["format"],'ok',100,'',$xml,'person','full',2);
				//$txt=OCSXML::generatexml($format,'failed',102,'data is private');
				echo($txt);
			}
		} else {
			$this->personsearch($format, $username, '','','','','','','','','','','','','');
		}
	}
    
    public function personsearch($format,$username,$country,$city,$description,$pc,$software,$longitude,$latitude,$distance,$attributeapp,$attributekey,$attributevalue,$page,$pagesize)
    {
			$pl = new OCSPersonLister;
			$xml = $pl->ocs_person_search($username,$page,$pagesize);
			
			for($i=0;$i<count($xml);$i++){
				$xml[$i]['personid'] = $xml[$i]['login'];
				//unset($xml[$i]['login']);
			}
			
			$plcount = count($xml);
			
			$txt=OCSXML::generatexml(EConfig::$data["ocsserver"]["format"],'ok',100,'',$xml,'person','summary',2,$plcount,$pagesize);
			
			echo($txt);
	}
	
	////////////////////////////////////// CONTENT API /////////////////////////////////////////
	/**	 
	 * get a specific content
	 * @param string $format
	 * @param string $content
	 * @return string xml/json
	 */
	public function contentget($format,$content) {

		$user=$this->_checkpassword(false);
		//$this->checktrafficlimit($user);

		$content=addslashes($content);
		
		// fetch data
		$con = new OCSContent();

		// check data
		if (!$con->load($content)) {
			$txt=OCSXML::generatexml($format,'failed',101,'content not found');
		} else {
			$xml['id']=$con->id;
			$xml['name']=$con->name;
			$xml['version']=$con->version;
			$xml['typeid']=$con->type;
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
			$xml['license'] = $con->license;
			$xml['personid'] = $con->personid;
			$xml['preview1'] = $con->preview1;
			$xml['preview2'] = $con->preview2;
			$xml['preview3'] = $con->preview3;

			// download
			if (!empty($con->downloadname1) or !empty($con->downloadlink1)) {
				$xml['downloadname1'] = $con->downloadname1;
				$xml['downloadlink1'] = $con->downloadlink1;
			} else {
				$xml['downloadname1']='';
				$xml['downloadlink1']='';
			}
			
			$xml2[0]=$xml;
			$txt=OCSXML::generatexml($format,'ok',100,'',$xml2,'content','full',2);
			echo($txt);

		}
	}
	
	public  function contentdownload($format,$content,$item) {
			$user=$this->_checkpassword(false);
			//$this->checktrafficlimit($user);

			$content = (int) $content;
			$item = (int) $item;

			// item range
			if($item<1 or $item>12) {
				$txt=OCSXML::generatexml($format,'failed',103,'item not found');
			} else {

				// fetch data
				$con = new OCSContent();

				// check data
				if (!$con->load($content)) {
					$txt=OCSXML::generatexml($format,'failed',101,'content not found');
				} else {
						//download link
						$link = $con->downloadlink1;
						//if url is nonexistent or broken we just set mimetype to unknown
						//mimetype
						if(file_exists($link)){
							$headers = get_headers($link);
							$mimetype = $headers[3];
						} else {
							$mimetype = "application/unknown";
						}
						
						if (!empty($con->downloadname1) or !empty($con->downloadlink1)) {
							$xml['downloadlink']=$link;
							$xml['mimetype']=$mimetype;
							$xml2[0]=$xml;
							$txt=OCSXML::generatexml($format,'ok',100,'',$xml2,'content','download',2);
						} else {
							$txt=OCSXML::generatexml($format,'failed',103,'content item not found');
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
	public  function contentlist($format,$contents,$searchstr,$searchuser,$external,$distribution,$license,$sortmode,$page,$pagesize) {
		$user=$this->_checkpassword(false);
		//$this->checktrafficlimit($user);
		
		$conl = new OCSContentLister("ocs_content");
		$xml = $conl->ocs_content_list($searchstr,$sortmode,$page,$pagesize,$searchuser);
		$totalitems = $conl->get_totalitems();
		/*
		 * test page: http://localhost/v1/content/data?search=lolol
		 */
		
		if(empty($xml)){
			$txt=OCSXML::generatexml($format,'ok',100,'');
		} else {
			$txt=OCSXML::generatexml($format,'ok',100,'',$xml,'content','summary',2,$totalitems,$pagesize);
		}
		
		echo($txt);	
	}
	
	/**	 
	 * get a list of contents categories
	 * @param string $format
	 * @return string xml/json
	 */
	public  function contentcategories($format) {
		$user=$this->_checkpassword(false);
		//$this->checktrafficlimit($user);

		$i=0;
		foreach(EConfig::$data["ocs_categories"] as $key=>$value) {
			$i++;
			$xml[$i]['id']=$key;
			$xml[$i]['name']=$value;
		}
		$txt=OCSXML::generatexml($format,'ok',100,'',$xml,'category','',2,count(EConfig::$data["ocs_categories"]));

		echo($txt);
	}
	
	/**	 
	 * get a list of contents licenses
	 * @param string $format
	 * @return string xml/json
	 */
	private function contentlicenses($format) {
		$contentlicense = EConfig::$data["licenses"];
		$contentlicenselink = EConfig::$data["licenseslink"];

		$user=$this->_checkpassword(false);
		//$this->checktrafficlimit($user);

		$i=0;
		foreach($contentlicense as $key=>$value) {
			$i++;
			$xml[$i]['id']=$key;
			$xml[$i]['name']=$value;
			$xml[$i]['link']=$contentlicenselink[$key];
		}
		$txt=OCSXML::generatexml($format,'ok',100,'',$xml,'license','',2,count($contentlicense));

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
		
		$user=$this->_checkpassword(true);
		//$this->checktrafficlimit($user);
		
		$con = new OCSContent();
		
		// fetch data
		$content=addslashes($content);
		$vote=addslashes($vote);
		
		// check data
		if (!$con->load($content)) {
			$txt=OCSXML::generatexml($format,'failed',101,'content not found');
		} else {
			if($user<>'') $con->set_score($vote);
			$txt=OCSXML::generatexml($format,'ok',100,'');
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
		$user=$this->_checkpassword(true);
		//$this->checktrafficlimit($user);
		$content=addslashes($contentid);
		$preview=addslashes($previewid);

		// fetch data
		$con = new OCSContent();

		if($con->load($content)){
			if($con->is_preview_available($previewid)){
				if($con->is_owned(OCSUser::id())) {
					
					$con->previewdelete($content,$preview);
					
					$txt=OCSXML::generatexml($format,'ok',100,'');
				} else {
					$txt=OCSXML::generatexml($format,'failed',101,'no permission to change content');
				}
			} else {
				$txt=OCSXML::generatexml($format,'failed',102,'preview not found');
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
		$user=$this->_checkpassword(true);
		//$this->checktrafficlimit($user);
		$content=addslashes($contentid);
		$preview=addslashes($previewid);

		// fetch data
		$con = new OCSContent();

		if(($preview==1) or ($preview==2) or ($preview==3)) {

			if($con->load($content) and $con->is_owned(OCSUser::id())) {

				if(isset($_FILES['localfile']['name']) and isset($_FILES['localfile']['name']) and ($_FILES['localfile']['name']<>'' and $_FILES['localfile']['name']<>'none' and $_FILES['localfile']['tmp_name']<>'' and $_FILES['localfile']['tmp_name']<>'none')) {
					if($con->previewadd($content,'localfile',$preview)){
						$txt=OCSXML::generatexml($format,'ok',100,'');
					} else {
						ELog::error("previewadd crashed lol!");
					}
				} else {
					$txt=OCSXML::generatexml($format,'failed',101,'localfile not found');
				}
			} else {
				$txt=OCSXML::generatexml($format,'failed',102,'no permission to change content');
			}
		} else {
			$txt=OCSXML::generatexml($format,'failed',103,'preview must be 1, 2 or 3');
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
		$user=$this->_checkpassword(true);
		//$this->checktrafficlimit($user);
		$content=addslashes($contentid);

		// fetch data
		$con = new OCSContent();

		if($con->load($content) and $con->is_owned(OCSUser::id())) {

			$con->downloaddelete();
			$txt=OCSXML::generatexml($format,'ok',100,'');
		} else {
			$txt=OCSXML::generatexml($format,'failed',101,'no permission to change content');
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
		
		$user=$this->_checkpassword(true);
		//$this->checktrafficlimit($user);
		$content=addslashes($contentid);

		// fetch data
		$con = new OCSContent();

		if($con->load($content) and $con->is_owned(OCSUser::id())) {
		
			if(isset($_FILES['localfile']['name']) and isset($_FILES['localfile']['name']) and ($_FILES['localfile']['name']<>'' and $_FILES['localfile']['name']<>'none' and $_FILES['localfile']['tmp_name']<>'' and $_FILES['localfile']['tmp_name']<>'none')) {
				if($con->downloadadd($content,'localfile')){
					$txt=OCSXML::generatexml($format,'ok',100,'');
				}else{
					$txt=OCSXML::generatexml($format,'failed',101,$error);
				} 
			} else {
				$txt=OCSXML::generatexml($format,'failed',102,'localfile not found');
			}
		} else {
			$txt=OCSXML::generatexml($format,'failed',103,'no permission to change content');
		}

		echo($txt);

	}

	/**	 
	 * add a new content
	 * @param string $format
	 * @return string xml/json
	 */
	private  function contentadd($format) {
		$user=$this->_checkpassword(true);
		//$this->checktrafficlimit($user);
		
		$categories = EConfig::$data["ocs_categories"];
		$numcats = count($categories);
		
		if(OCSUser::is_logged()) {

			$data=array();
			$data['name']=$this->readdata('name','text');
			$data['type']=$this->readdata('type','int');
			
			if($this->readdata('downloadname1','text')<>'')	$data['downloadname1']=$this->readdata('downloadname1','text') ;
			if($this->readdata('downloadlink1','text')<>'')			$data['downloadlink1']=$this->readdata('downloadlink1','text');
			if($this->readdata('description','text')<>'') { $data['description']=$this->readdata('description','text'); } else { $data['description']='...'; }
			if($this->readdata('summary','text')<>'') { $data['summary']=$this->readdata('summary','text'); } else { $data['summary']='...'; }
			if($this->readdata('version','text')<>'') { $data['version']=$this->readdata('version','text'); } else { $data['version']='...'; }
			if($this->readdata('changelog','text')<>'') { $data['changelog']=$this->readdata('changelog','text'); } else { $data['changelog']='...'; }
			//if($this->readdata('personid','text')<>'')			$data['personid']=$this->readdata('personid','text');
			if($this->readdata('license','int') >=0 or $this->readdata('license','int')<5)  $data['license']=$this->readdata('license','int');
			
			/*
			$data['preview1'] = "http://".EConfig::$data["ocs"]["host"]."/template/img/screenshot-unavailable.png";
			$data['preview2'] = "http://".EConfig::$data["ocs"]["host"]."/template/img/screenshot-unavailable.png";
			$data['preview3'] = "http://".EConfig::$data["ocs"]["host"]."/template/img/screenshot-unavailable.png";
			*/
			$data['preview1'] = "";
			$data['preview2'] = "";
			$data['preview3'] = "";
			$data['personid'] = $user;
			
			if( ($data['name']<>'') or ($data['type']<0) or ($data['type']>$numcats) ) {
				$content = new OCSContent();
				$content->set_owner(OCSUser::id());
				$content->set_data($data);
				$content->save();
				
				$xml = array();
				$xml[0]['id'] = $content->id();
				$txt = OCSXML::generatexml($format,'ok',100,'',$xml,'content','',2);
			}else{
				$txt = OCSXML::generatexml($format,'failed',101,'please specify all mandatory fields');
			}
		}else{
			$txt=OCSXML::generatexml($format,'failed',102,'no permission to change content');
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
		
		$user=$this->_checkpassword(true);
		//$this->checktrafficlimit($user);
		$content=addslashes($contentid);
		
		$categories = EConfig::$data["ocs_categories"];
		$numcats = count($categories);
		
		// fetch data
		$con = new OCSContent();
		if($con->load($content) and OCSUser::is_logged() and OCSUser::id() == $con->owner) {

			$data=array();
			if($this->readdata('name','text')<>'')		$data['name'] = $this->readdata('name','text');
			if($this->readdata('type','text')<>'')		$data['type'] = $this->readdata('type','text'); else $data['type'] = $con->type;
			
			if($this->readdata('downloadname1','text')<>$con->downloadname1)		$data['downloadname1'] = $this->readdata('downloadname1','text');
			if($this->readdata('downloadlink1','text')<>$con->downloadlink1)		$data['downloadlink1'] = $this->readdata('downloadlink1','text');
			if($this->readdata('description','text')<>'') { $data['description']=$this->readdata('description','text'); } else { $data['description']='...'; }
			if($this->readdata('summary','text')<>'') { $data['summary']=$this->readdata('summary','text'); } else { $data['summary']='...'; }
			if($this->readdata('version','text')<>'') { $data['version']=$this->readdata('version','text'); } else { $data['version']='...'; }
			if($this->readdata('changelog','text')<>'') { $data['changelog']=$this->readdata('changelog','text'); } else { $data['changelog']='...'; }
			if($this->readdata('license','int') >=0 or $this->readdata('license','int')<5)  $data['license']=$this->readdata('license','int');
			
			if( ($data['name']<>'') or ($data['type']<0) or ($data['type']>$numcats) ) {
				$con->update(array("name","type","downloadname1","downloadlink1","description","summary","version","changelog","license"));
				
				$xml = array();
				$txt = OCSXML::generatexml($format,'ok',100,'',$xml,'content'); 
			}else{
				$txt = OCSXML::generatexml($format,'failed',101,'please specify all mandatory fields');
			}
		}else{
			$txt=OCSXML::generatexml($format,'failed',102,'no permission to change content');
		}
		$con->updated();

		echo($txt);

	}

	/**	 
	 * delete a content
	 * @param string $format
	 * @param string $contentid
	 * @return string xml/json
	 */
	private  function contentdelete($format,$contentid) {
		
		$user=$this->_checkpassword(true);
		//$this->checktrafficlimit($user);
		$content=addslashes($contentid);
		
		// fetch data
		$con = new OCSContent();
		if(!$con->load($content)){
			$txt=OCSXML::generatexml($format,'failed',101,'no permission to change content');
		} else {
			if(!$con->is_owned(OCSUser::id())){
				$txt=OCSXML::generatexml($format,'failed',101,'no permission to change content');
			} else {
				$con->delete();
				$txt=OCSXML::generatexml($format,'ok',100,'');
			}
		}
		
		echo($txt);
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

		$user=$this->_checkpassword();
		//$this->checktrafficlimit($user);
		
		$al = new OCSActivityLister();
        $log=$al->ocs_activity_list($user,$page,$pagesize);
        $itemscount=count($log);
        $xml=array();
        for ($i=0; $i < $itemscount;$i++) {
            $xml[$i]['id']=$log[$i]['id'];
            $xml[$i]['personid']=$log[$i]['personid'];
            $xml[$i]['firstname']=$log[$i]['firstname'];
            $xml[$i]['lastname']=$log[$i]['lastname'];
            $xml[$i]['profilepage']='';
            $xml[$i]['avatarpic']='';
            $xml[$i]['timestamp']=date('c',$log[$i]['timestamp']);
            $xml[$i]['type']=$log[$i]['type'];
            $xml[$i]['message']=strip_tags($log[$i]['message']);
            $xml[$i]['link']='';
        }

        $txt=OCSXML::generatexml($format,'ok',100,'',$xml,'activity','full',2,count($xml),$pagesize);

        echo($txt);

	}

	/**	 
	 * submit a activity
	 * @param string $format
	 * @param string $message
	 * @return string xml/json
	 */
	private  function activityput($format,$message) {
		$user=$this->_checkpassword();
		//$this->checktrafficlimit($user);

		if($user<>'') {
			if(trim($message)<>'') {
				OCSActivity::add(OCSUser::id(), 1, $message);
				echo(OCSXML::generatexml($format,'ok',100,''));
			} else {
				echo(OCSXML::generatexml($format,'failed',101,'empty message'));
			}
		} else {
			echo(OCSXML::generatexml($format,'failed',102,'user not found'));
		}

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
		$user=$this->_checkpassword(true);
		//$this->checktrafficlimit($user);
		$content=strip_tags(addslashes($content));
		$page = intval($page);
		
		$fan = new OCSFanLister;
		$xml = $fan->ocs_fan_list($content,$page,$pagesize);
		$fancount = count($xml);
		$txt=OCSXML::generatexml($format,'ok',100,'',$xml,'person','fans',2,$fancount,$pagesize);
		
		echo $txt;
	}


	/**	 
	 * add a fans to a specific content
	 * @param string $format
	 * @param string $content
	 * @return string xml/json
	 */
	private  function addfan($format,$content) {
		$contentid = intval($content);
		$user=$this->_checkpassword(true);
		//$this->checktrafficlimit($user);
		
		$fan = new OCSFan;
		if(!$fan->isfan($content)){
			$fan->add($contentid);
		}
		
		$txt=OCSXML::generatexml($format,'ok',100,'');
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
		$user=$this->_checkpassword(true);
		//$this->checktrafficlimit($user);
		
		$fan = new OCSFan;
		if($fan->isfan($content)){
			$fan->remove($contentid);
		}
		
		$txt=OCSXML::generatexml($format,'ok',100,'');
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
		$user=$this->_checkpassword(true);
		//$this->checktrafficlimit($user);
		$fan = new OCSFan;
		if($fan->isfan($contentid)){
			$xml['status']='fan';
			$txt=OCSXML::generatexml($format,'ok',100,'',$xml,'','',1); 
		}else{
			$xml['status']='notfan';
			$txt=OCSXML::generatexml($format,'ok',100,'',$xml,'','',1); 
		}
		echo($txt);
	}
	
	// COMMENTS API ############################################# TODO: tests

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
		$user = $this->_checkpassword(true);
		//$this->checktrafficlimit($user);
		$data['parent'] = strip_tags(addslashes($parent));
		$data['subject'] = strip_tags(addslashes($subject));
		$data['message'] = strip_tags(addslashes($message));
		$data['content'] = strip_tags(addslashes($content));
		$data['content2'] = strip_tags(addslashes($content2));
		$data['type'] = strip_tags(addslashes($type));
		$data['owner'] = OCSUser::id();

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
					echo(OCSXML::generatexml($format,'ok',100,'',$xml,'comment','',2));
				} else {
					echo(OCSXML::generatexml($format,'failed',101,'content must not be empty'));
				}
			} else {
				echo(OCSXML::generatexml($format,'failed',102,'message or subject must not be empty'));
			}
		} else {
			echo(OCSXML::generatexml($format,'failed',103,'no permission to add a comment'));
		}

	}



	private  function commentsget($format,$type,$content,$content2,$page,$pagesize) {
		$user=$this->_checkpassword(false);
		//$this->checktrafficlimit($user);
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
		//$txt=$this->generatexml($format,'ok',100,'',$comments,'event','detail',2,$totalitems,$pagesize);

		$txt=OCSXML::generatexml($format,'ok',100,'',$comments,'comment','','dynamic',$totalitems,$pagesize);
		echo($txt);


	}


	/**	 
	 * vote for a comment TODO: IMPLEMENT THIS ONE
	 * @param string $format
	 * @param string $id
	 * @param string $score
	 * @return string xml/json
	 */
	private  function commentvote($format,$id,$score) {
		$user=$this->_checkpassword(true);
		//$this->checktrafficlimit($user);
		
		$comment = new OCSComment();
		if($comment->load($id)){
			
			$comment->set_score($score);
			$txt=$this->generatexml($format,'ok',100,'');
			echo($txt);
		} else {
			$txt=$this->generatexml($format,'failed',101,'comment not found');
		}
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
		$user=$this->_checkpassword();
		//$this->checktrafficlimit($user);
        
        $friend = new OCSFriendsLister;
        $xml = $friend->ocs_sentinvitations($page,$pagesize);
        $friendcount = count($xml);
        $txt=OCSXML::generatexml($format,'ok',100,'',$xml,'person','id',2,$friendcount,$pagesize);
        
        echo $txt;
	}

	/**	 
	 * get the list of received invitations
	 * @param string $format
	 * @param string $page
	 * @param string $pagesize
	 * @return string xml/json
	 */
	private  function friendreceivedinvitations($format,$page,$pagesize) {
		$user=$this->_checkpassword();
		//$this->checktrafficlimit($user);

        $friend = new OCSFriendsLister;
        $xml = $friend->ocs_receivedinvitations($page,$pagesize);
        $friendcount = count($xml);
        $txt=OCSXML::generatexml($format,'ok',100,'',$xml,'person','id',2,$friendcount,$pagesize);
        
        echo $txt;
	}

	/**	 
	 * get the list of friends from a person
	 * @param string $format
	 * @param string $fromuser user which called the query
	 * @param string $page
	 * @param string $pagesize
	 * @return string xml/json
	 */
	private  function friendget($format,$fromuser,$page,$pagesize) { //example params: (,snizzo,0,10);
		$user=$this->_checkpassword();
		//$this->checktrafficlimit($user);
		
		$fromuser=strip_tags(addslashes($fromuser));
        
        /*
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
		*/
        $fan = new OCSFriendsLister;
        $xml = $fan->ocs_friend_list($fromuser,$page,$pagesize);
        $friendcount = count($xml);
        $txt=OCSXML::generatexml($format,'ok',100,'',$xml,'person','id',2,$friendcount,$pagesize);
        
        echo $txt;
	}




	/**	 
	 * invite a person as a friend
	 * @param string $format
	 * @param string $inviteuser
	 * @param string $message
	 * @return string xml/json
	 */
	private  function friendinvite($format,$inviteuser,$message) {
		$user=$this->_checkpassword();
		//$this->checktrafficlimit($user);
		$inviteuser = strip_tags(addslashes($inviteuser));
		$message = strip_tags(addslashes($message));

		if($user<>'' and $inviteuser<>'' and $inviteuser<>false) {
			if($user<>$inviteuser) {
				if($message<>'') {
					OCSFriend::send_invitation($inviteuser, $message);
					echo(OCSXML::generatexml($format,'ok',100,''));
				} else {
					echo(OCSXML::generatexml($format,'failed',101,'message must not be empty'));
				}
			}else{
				echo(OCSXML::generatexml($format,'failed',102,'you can\´t invite yourself'));
			}
		} else {
			echo(OCSXML::generatexml($format,'failed',103,'user not found'));
		}
		
	}

	/**	 
	 * approve a friendsship invitation
	 * @param string $format
	 * @param string $inviteuser
	 * @return string xml/json
	 */
	private  function friendapprove($format,$inviteuser) {
		$user=$this->_checkpassword();
		//$this->checktrafficlimit($user);
		$inviteuser = strip_tags(addslashes($inviteuser));

		if($user<>'' and $inviteuser<>'') {
			OCSFriend::approve_invitation($inviteuser);
			echo(OCSXML::generatexml($format,'ok',100,''));
		} else {
			echo(OCSXML::generatexml($format,'failed',101,'user not found'));
		}

	}


	/**	 
	 * decline a friendsship invitation
	 * @param string $format
	 * @param string $inviteuser
	 * @return string xml/json
	 */
	private  function frienddecline($format,$inviteuser) {
		$user=$this->_checkpassword();
		//$this->checktrafficlimit($user);
		$inviteuser = strip_tags(addslashes($inviteuser));

		if($user<>'' and $inviteuser<>'') {
			OCSFriend::decline_invitation($inviteuser);
			echo(OCSXML::generatexml($format,'ok',100,''));
		} else {
			echo(OCSXML::generatexml($format,'failed',101,'user not found'));
		}

	}


	/**	 
	 * cancel a friendsship
	 * @param string $format
	 * @param string $inviteuser
	 * @return string xml/json
	 */
	private  function friendcancel($format,$inviteuser) {
		$user=$this->_checkpassword();
		//$this->checktrafficlimit($user);
		$inviteuser = strip_tags(addslashes($inviteuser));

		if($user<>'' and $inviteuser<>'') {
			OCSFriend::cancel_friendship($inviteuser);
			echo(OCSXML::generatexml($format,'ok',100,''));
		} else {
			echo(OCSXML::generatexml($format,'failed',101,'user not found'));
		}

	}


	/**	 
	 * cancel a friendsship invitation
	 * @param string $format
	 * @param string $inviteuser
	 * @return string xml/json
	 */
	private  function friendcancelrequest($format,$inviteuser) {
		$user=$this->_checkpassword();
		//$this->checktrafficlimit($user);
		$inviteuser = strip_tags(addslashes($inviteuser));

		if($user<>'' and $inviteuser<>'') {
			H01_RELATION::deleterelationrequest(1,$user,$inviteuser,CONFIG_USERDB);
			echo(OCSXML::generatexml($format,'ok',100,''));
		} else {
			echo(OCSXML::generatexml($format,'failed',101,'user not found'));
		}

	}
    
}

?>
