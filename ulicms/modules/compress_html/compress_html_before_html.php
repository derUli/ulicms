<?php

    function compress_html($compress)
    {
        $i = array('/>[^S ]+/s','/[^S ]+</s','/(s)+/s');
        $ii = array('>','<','1');
        return preg_replace($i, $ii, $compress);
    }
    
    
    //  start output buffer
    ob_start('compress_html');
    

