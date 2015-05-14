<?php
/*
 * on this file gfx inclusion is useless as gfx is already running
 */

class InstallController extends EController
{
	
    public function index()
    {
		$data = array();
		$data['status'] = 'inactive'; //set inactive to default
        EStructure::view("install/index", $data);
    }
    
    public function action($str)
    {
		echo "<br>";
		var_dump($str);
	}
}

?>
