<?php

    function compress_html($compress)
    {
        $i = array('/\s\s+/');
        $compress = preg_replace($i, " ", $compress);
        $ii = array('\r\n');
        $compress = preg_replace($ii, "", $compress);
        $iii = array('\n');
        $compress = preg_replace($iii, "", $compress);
        
        return $compress;
    }
    
    
    //  start output buffer
    ob_start('compress_html');
    

