<?php
/*
 * on this file gfx inclusion is useless as gfx is already running
 */

class StatusController extends EController
{
	
    public function index($args)
    {	
        EStructure::view("header");
        
        echo "<h3>Server</h3>";
        
        EStructure::view("footer");
    }
    
    public function database($args)
    {
		EStructure::view("header");
        
        if(isset($args[0])){
			if($args[0]=='reset'){
				echo '<h3>Database</h3>';
				echo 'Resetting...Done!';
				//code that resets database
			}
		} else {
			echo '<h3>Database</h3>';
			echo '<p>If you have a broken/unconsistent database running, you can attempt resetting your database.<br>
			<a href="/admin/status/database/reset">RESET DATABASE</a></p>';
		}
        
        EStructure::view("footer");
	}
	
	public function categories()
    {
		EStructure::view("header");
        
        echo '<h3>Categories</h3>';
        echo '<p>Here you can manage OCS categories:</p>';
        //code that prints database category table
        
        EStructure::view("footer");
	}
    
}

?>
