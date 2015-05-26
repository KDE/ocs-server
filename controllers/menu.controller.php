<?php
/*
 * on this file gfx inclusion is useless as gfx is already running
 */

class MenuController extends EController
{
    public function index($args)
    {	
        EStructure::view("adminmenu/index");
    }   
}

?>
