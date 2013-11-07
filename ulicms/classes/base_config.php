<?php 
class baseConfig{
    function getVar($var){
      return getconfig(db_escape($var));
    }
   
    function setVar($var, $value){
         return setconfig(db_escape($var), db_escape($value));
    }

}