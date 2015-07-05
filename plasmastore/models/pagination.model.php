<?php
class PaginationModel extends EModel {
	public function pagination($args) { 
		$dat = new RetrieveModel();
		$data = $dat->getData();
		$pagenumber=$args;
		$numberOfPages=ceil($data["ocs"]["meta"]["totalitems"]/$data["ocs"]["meta"]["itemsperpage"]);
		echo "<div class=\"row text-center\">
					            <div class=\"col-lg-12\">
					                <ul class=\"pagination\" id=\"pagebuttons\">
					                <li>
					                        <a href=\"/plasmastore/home/page/1\">&laquo;</a>
					                    </li>";
		if($numberOfPages<6){
			for ($i=1; $i<=$numberOfPages; $i++) {
				if($pagenumber==$i){
					echo "                    
		                <li class=\"active\">
		                    <a href=\"/plasmastore/home/page/$i\">$i</a>
		                </li>";
		        } else {
		            	echo "
			            	 <li>
			                     <a href=\"/plasmastore/home/page/$i\">$i</a>
			                 </li>";
			     }
			}
		}
		else {
			if ($pagenumber==1) {
				echo "
				<li class=\"active\">
		            <a href=\"/plasmastore/home/page/".$pagenumber."\">$pagenumber</a>
		        </li>";
			}
			else if ($pagenumber==2) {
				echo "
				<li>
		            <a href=\"/plasmastore/home/page/".($pagenumber-1)."\">".($pagenumber-1)."</a>
		        </li>
				<li class=\"active\">
		            <a href=\"/plasmastore/home/page/".$pagenumber."\">$pagenumber</a>
		        </li>";
		    }
		    else {
			echo "
				<li>
		            <a href=\"/plasmastore/home/page/".($pagenumber-1)."\">".($pagenumber-1)."</a>
		        </li>
				<li class=\"active\">
		            <a href=\"/plasmastore/home/page/".$pagenumber."\">$pagenumber</a>
		        </li>";
		    }
		        if ($numberOfPages-2>=$pagenumber+2){ //pagine indietro
		        	if($pagenumber==1){
		        		echo "
		        	<li>
		            	<a href=\"/plasmastore/home/page/".($pagenumber+1)."\">".($pagenumber+1)."</a>
		        	</li>
		        	<li>
		            	<a href=\"/plasmastore/home/page/".($pagenumber+2)."\">".($pagenumber+2)."</a>
		        	</li>";
		        	}
		        	else {
		        	echo"
		        	<li>
		            	<a href=\"/plasmastore/home/page/".($pagenumber+1)."\">".($pagenumber+1)."</a>
		        	</li>";
		        }
		        echo "
		        	<li>
		            	<a>...</a>
		        	</li>
		        	<li>
		            	<a href=\"/plasmastore/home/page/".($numberOfPages-1)."\">".($numberOfPages-1)."</a>
		        	</li>
		        	<li>
		            	<a href=\"/plasmastore/home/page/$numberOfPages\">$numberOfPages</a>
		        	</li>";

		        }
		        else if ($pagenumber+2<=$numberOfPages){
		        	echo "
		        	<li>
		            	<a href=\"/plasmastore/home/page/".($pagenumber+1)."\">".($pagenumber+1)."</a>
		        	</li>
					<li>
		            	<a href=\"/plasmastore/home/page/".($pagenumber+2)."\">".($pagenumber+2)."</a>
		        	</li>";
		        }
		        else if ($pagenumber+1==$numberOfPages){
		        	echo "
		        	<li>
		            	<a href=\"/plasmastore/home/page/".($pagenumber+1)."\">".($pagenumber+1)."</a>
		        	</li>";
		        }
		}
	
		echo "
		    <li>
		        <a href=\"/plasmastore/home/page/$numberOfPages\">&raquo;</a>
		    </li>
		            </ul>
		        </div>
		    </div>";
	}
}

