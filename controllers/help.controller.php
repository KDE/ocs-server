<?php
/*
 * on this file gfx inclusion is useless as gfx is already running
 */

class HelpController extends EController
{
	
    public function index($args)
    {
        EStructure::view("help/index");
    }
    
    public function prova($args)
    {
		echo $args[0];
	}
    
}

?>
