<?php
class httpRequestHelper{
     public static function is_ssl(){
         return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
             || $_SERVER['SERVER_PORT'] == 443);
         }
    
     public static function get_ip()
    {
         $ip = '';
         $sources = array (
            'REMOTE_ADDR',
             'HTTP_X_FORWARDED_FOR',
             'HTTP_CLIENT_IP');
        
         foreach ($sources as $source){
             if (isset ($_SERVER[ $source ])){
                 $ip = $_SERVER[ $source ];
                 }elseif (getenv($source)){
                 $ip = getenv($source);
                 }
             }
        
         return $ip;
        }
    
     }
