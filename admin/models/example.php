<?php

class ExampleModel extends EModel
{
        public function __construct()
        {
                parent::__construct("posts");
        }
        
        public function getNear($x, $y, $z, $factor)
        {
			$xu = $x+$factor;
			$xl = $x-$factor;
			
			$yu = $y+$factor;
			$yl = $y-$factor;
			
			$zu = $z+$factor;
			$zl = $z-$factor;
			
			$data = $this->find("*", "where x > $xl and x < $xu and y > $yl and y < $yu and z > $zl and z < $zu ORDER BY up DESC, id DESC");
			
			return $data;
        }
        
        public function voteup($id)
        {
			$q = "UPDATE posts SET up = up+1 WHERE id = ".$id." LIMIT 1";
			$r = EDatabase::q($q);
		}
        
        public function votedown($id)
        {
			$q = "UPDATE posts SET down = down+1 WHERE id = ".$id." LIMIT 1";
			$r = EDatabase::q($q);
		}
        
        public function add()
        {
			$this->insert(array("x", "y", "z", "body", "owner"));
        }
        
        public function clearAll()
        {
			$this->delete();
		}
        
}

?>
