<?php
/*
 * on this file gfx inclusion is useless as gfx is already running
 */

class HelpController extends EController
{
	
    public function index()
    {
        EStructure::view("help/index");
    }
    
}

?>
