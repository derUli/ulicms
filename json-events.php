<?php
        include "init.php";

	$events = array();

	$query = mysql_query("SELECT * FROM `".tbname("events")."` ORDER BY id");
	
	while($row = mysql_fetch_object($query)){
          $obj = array();	
          $obj["id"] = $row->id;
          
  
          
          $obj["start"] = date("Y-m-d", $row->start);
          $obj["end"] = date("Y-m-d", $row->end);
          $obj["title"] = $row->title;
          
          if(!empty($row->url) and $row->url != "http://")
             $obj["url"] = $row->url;;
          
          array_push($events, $obj);
	
	}
	

	echo json_encode($events);

?>
