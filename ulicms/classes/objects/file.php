<?php 
class File{
   public static function write($file, $data){
      return file_put_contents($file, $data);
   }
   
   public static function append($file, $data){
      return file_put_contents($file, $data, FILE_APPEND | LOCK_EX);
   }
   
   public static function read($file){
      return file_get_contents($file);
   }
}