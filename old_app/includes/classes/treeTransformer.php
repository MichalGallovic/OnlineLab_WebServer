<?php 

class TreeTransformer {
   
   var $table;
   
   function __construct($table) {
     global $mysql;
	 $temp_link = array();
	 $this->count=1;
	 $this->table = $table;
	 
	 $result = $mysql->query("SELECT * FROM ".$table." ");
	  while($raw = $mysql->fetch_array($result)){
		$rawLink[] = $raw;
	  }
	  
	  
	  foreach($rawLink as $k=>$row) {
		$parent=$row["parent_id"];
		$child=$row["id"];
		
		if (!array_key_exists($parent,$temp_link)) {
		 $temp_link[$parent]=array();
		}
		$temp_link[$parent][]=$child;
	  }
	  
	 $this->link=$temp_link;
   	
   }

   function repaireNestedSet($root_id) {
     $lft=$this->count;
     $this->count++;

     $kid=$this->getChildren($root_id);
     if ($kid) {
       foreach($kid as $c) {
         $this->repaireNestedSet($c);
      }
     }
     $rgt=$this->count;
     $this->count++;
     $this->write($lft,$rgt,$root_id);
   }

   function getChildren($id) {
      return @$this->link[$id];
   }

   function write($lft,$rgt,$id) {
     
	 global $mysql;
     
	 $mysql->query("UPDATE ".$this->table." 
	 					SET 
	 						lft = '".$lft."' , 
							rght = '".$rgt."' 
					WHERE id = '".$id."' ");
   }
  
}
?>