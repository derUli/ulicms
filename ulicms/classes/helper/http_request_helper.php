<?php 
class httpRequestHelper{
  public static function is_ssl(){
     return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
         || $_SERVER['SERVER_PORT'] == 443);
     }

}