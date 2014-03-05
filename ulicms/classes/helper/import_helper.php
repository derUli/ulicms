<?php
class ImportHelper{
    
    var $errors = null;
    
    public function __construct(){
         $this -> errors = array();
        
        }
    
    public function getErrors(){
         return $this -> errors;
        }
    
    private function logerror($txt, $id = null){
         if(!is_null($id)){
             $txt = strval($id) . ": " . $txt;
             }
         $this -> errors[] = $txt;
        }
    
    public function importJSON($target, $json, $doUpdate = true){
        set_time_limit(0);
         $data = json_decode($json, true);
         for($i = 0; $i < count($data); $i++){
             $fields = array();
             $values = array();
             $insert = false;
             $sql = "";
             $ds = $data[$i];
             if($ds["id"]){
                 $id = intval($ds["id"]);
                 }else{
                 $id = 0;
                 }
            
             $query = db_query("SELECT * FROM " . $target . " WHERE id=" . $id);
            
             foreach($ds as $key => $value){
                 if(db_num_rows($query) > 0){
                     if($key != "id" and $id > 0 and $doUpdate){
                        
                         db_query("UPDATE " . $target . " SET `" . $key . "` = '" . db_escape($value) . "' WHERE id = $id")or $this -> logerror(db_error(), $id);
                         }
                     }else{
                    
                     $fields[] = $key;
                     $values[] = $value;
                     $insert = true;
                    
                     }
                
                 }
            
             if($insert and count($fields) > 0 and count($values) > 0){
                 $sql = "INSERT INTO " . $target . " (" . join(",", $fields) . ") VALUES (";
                 for($m = 0; $m < count($values); $m++){
                     $sql .= "'" . db_escape($values[$m]) . "'";
                     if($m != count($values) - 1){
                         $sql .= ",";
                         }
                     }
                 $sql .= ")";
                 db_query($sql)or $this -> logerror(db_error());
                 }
            
             }
        
        
         }
    
     }

