<?php
class ExportHelper{
    static function table2JSON($name){
        
         $sql = "SELECT * FROM " . $name;
        
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
                    foreach($row as $name => $value){
                         $dr[$name] = $value;
                        
                         }
                    
                     array_push($data, $dr);
                     }
                
                
                
                 }
            
             }
        
        
        
         return json_encode($data, true);
        
         }
    
     }

