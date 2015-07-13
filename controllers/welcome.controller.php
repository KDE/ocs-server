<?php

class WelcomeController extends EController
{
	public function index()
	{
		EStructure::view('welcome/index');
	}
}

?>
