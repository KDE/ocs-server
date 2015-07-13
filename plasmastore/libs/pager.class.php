<?php
class Pager {
	private $OCSCall="";
	private $domain="";
	private $currentPage = 1;

	public function __construct($domain, $call) {
		$this->domain=$domain;
		$this->OCSCall=$call;
		$url=$_SERVER['REQUEST_URI'];
		$tokens = explode('/', $url);
		if ($tokens[sizeof($tokens)-2]=="page"){
			$this->currentPage=$tokens[sizeof($tokens)-1];
		}
	}

	public function getData() {
		$client = new OCSClient();
		return $client->get($this->OCSCall);
		
	}
    public function pagination() { 
		$data = $this->getData();
		$pagenumber=$this->currentPage;
		$numberOfPages=ceil($data["ocs"]["meta"]["totalitems"]/$data["ocs"]["meta"]["itemsperpage"]);
		echo "<div class=\"row text-center\">
					            <div class=\"col-lg-12\">
					                <ul class=\"pagination\" id=\"pagebuttons\">
					                <li>
					                        <a href=\"/plasmastore/".$this->domain."/page/1\">&laquo;</a>
					                    </li>";
		if($numberOfPages<6){
			for ($i=1; $i<=$numberOfPages; $i++) {
				if($pagenumber==$i){
					echo "                    
		                <li class=\"active\">
		                    <a href=\"/plasmastore/".$this->domain."/page/$i\">$i</a>
		                </li>";
		        } else {
		            	echo "
			            	 <li>
			                     <a href=\"/plasmastore/".$this->domain."/page/$i\">$i</a>
			                 </li>";
			     }
			}
		}
		else {
			if ($pagenumber==1) {
				echo "
				<li class=\"active\">
		            <a href=\"/plasmastore/".$this->domain."/page/".$pagenumber."\">$pagenumber</a>
		        </li>";
			}
			else if ($pagenumber==2) {
				echo "
				<li>
		            <a href=\"/plasmastore/".$this->domain."/page/".($pagenumber-1)."\">".($pagenumber-1)."</a>
		        </li>
				<li class=\"active\">
		            <a href=\"/plasmastore/".$this->domain."/page/".$pagenumber."\">$pagenumber</a>
		        </li>";
		    }
		    else {
			echo "
				<li>
		            <a href=\"/plasmastore/".$this->domain."/page/".($pagenumber-1)."\">".($pagenumber-1)."</a>
		        </li>
				<li class=\"active\">
		            <a href=\"/plasmastore/".$this->domain."/page/".$pagenumber."\">$pagenumber</a>
		        </li>";
		    }
		        if ($numberOfPages-2>=$pagenumber+2){ //pagine indietro
		        	if($pagenumber==1){
		        		echo "
		        	<li>
		            	<a href=\"/plasmastore/".$this->domain."/page/".($pagenumber+1)."\">".($pagenumber+1)."</a>
		        	</li>
		        	<li>
		            	<a href=\"/plasmastore/".$this->domain."/page/".($pagenumber+2)."\">".($pagenumber+2)."</a>
		        	</li>";
		        	}
		        	else {
		        	echo"
		        	<li>
		            	<a href=\"/plasmastore/".$this->domain."/page/".($pagenumber+1)."\">".($pagenumber+1)."</a>
		        	</li>";
		        }
		        echo "
		        	<li>
		            	<a>...</a>
		        	</li>
		        	<li>
		            	<a href=\"/plasmastore/".$this->domain."/page/".($numberOfPages-1)."\">".($numberOfPages-1)."</a>
		        	</li>
		        	<li>
		            	<a href=\"/plasmastore/".$this->domain."/page/$numberOfPages\">$numberOfPages</a>
		        	</li>";

		        }
		        else if ($pagenumber+2<=$numberOfPages){
		        	echo "
		        	<li>
		            	<a href=\"/plasmastore/".$this->domain."/page/".($pagenumber+1)."\">".($pagenumber+1)."</a>
		        	</li>
					<li>
		            	<a href=\"/plasmastore/".$this->domain."/page/".($pagenumber+2)."\">".($pagenumber+2)."</a>
		        	</li>";
		        }
		        else if ($pagenumber+1==$numberOfPages){
		        	echo "
		        	<li>
		            	<a href=\"/plasmastore/".$this->domain."/page/".($pagenumber+1)."\">".($pagenumber+1)."</a>
		        	</li>";
		        }
		}
	
		echo "
		    <li>
		        <a href=\"/plasmastore/".$this->domain."/page/$numberOfPages\">&raquo;</a>
		    </li>
		            </ul>
		        </div>
		    </div>";
	}




}

