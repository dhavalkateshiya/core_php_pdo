<?php 
class category extends DB
{
	
	public function getCategory()
	{
		$query  =  "SELECT * FROM `category` ";  
		$param  =parent::selectQuery($query);
		return $param;
	} 

	public function getParentCategory()
	{		 
		$query  =  "SELECT * FROM `category` WHERE cat_relation_id LIKE '%0%'";  
		$param  =parent::selectQuery($query);
		return $param;
	}  
	
	public function getChildCategory($pid)
	{		 
		$query  =  "SELECT * FROM `category` WHERE cat_relation_id LIKE '%$pid%'";  
		$param  =parent::selectQuery($query);
		return $param;
	} 
	  
}	