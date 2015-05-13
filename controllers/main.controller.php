<?php
/*
 * on this file gfx inclusion is useless as gfx is already running
 */

class MainController extends EController
{
	
    public function index()
    {
        EStructure::view("home");
    }
    
}

?>
