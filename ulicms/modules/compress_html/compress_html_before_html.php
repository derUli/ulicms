<?php

    function compress_html($compress)
    {
        $i = array('/\s\s+/');
        $compress = preg_replace($i, " ", $compress);
        $compress = str_replace("\r\n", "", $compress);
        $compress = str_replace("\n", "", $compress);
        
        return $compress;
    }
    
    
    //  start output buffer
    ob_start('compress_html');
    

