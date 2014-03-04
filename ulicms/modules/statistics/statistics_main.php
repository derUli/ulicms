<?php
function statistics_render(){
     $data = db_query("SELECT * FROM " . tbname("statistics") . " ORDER by date ASC");
     $visitor_total = db_num_rows($data);
     return intval($visitor_total);
     }
?>