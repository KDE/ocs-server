<?php

class ReportsModel extends EModel
{
        public function __construct()
        {
                parent::__construct("reports");
        }
        
        public function add()
        {
			$this->insert(array("target"));
		}
}

?>
