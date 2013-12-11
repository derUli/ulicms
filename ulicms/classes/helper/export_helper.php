<?php
class ExportHelper{
static function table2JSON($name){
    
    $sql = "SELECT * FROM ".$name;
     
     foreach($sql_query as $sql){
         $query = false;
         $query = db_query($sql);
        
         if(!$query){
             return null;
         }
         
         
         $data = array();
        
        
         if($query !== false and $query !== true){
             $fields_num = db_num_fields($query);
             if($fields_num){
                 
                 $data = array();
       
                 // printing table rows
                while($row = db_fetch_assoc($query))
                {
                        $dr = array();
                    
                     // $row is array... foreach( .. ) puts every element
                    // of $row to $cell variable
                    foreach($d as $name=>$value){
                        $dr[$name] = $value;
                         
                         }
                         
                    array_push($data, $dr);
                     }
                
             
                
                 }
            
             }
        
         }
    
     return json_encode($data);
    
     }
     
     }

