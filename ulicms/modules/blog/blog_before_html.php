<?php
if(isset($_GET["single"])){
     $single = mysql_real_escape_string($_GET["single"]);
     $query = db_query("SELECT * FROM `" . tbname("blog") . "` WHERE seo_shortname='$single'");
    
     if($query){
        
         if(mysql_num_rows($query) > 0){
             $result = mysql_fetch_assoc($query);
             $datum = $result["datum"];
            
            
             if(containsModule(get_requested_pagename(), "blog"))
                 header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $datum) . ' GMT');
            
             }
         }
    }
?>