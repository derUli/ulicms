<?php 
class baseConfig{
    public function getVar($var){
      return getconfig(db_escape($var));
    }
   
    public function setVar($var, $value){
         return setconfig(db_escape($var), db_escape($value));
    }

}
