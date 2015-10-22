<?php
// Bevor der erste Content ausgegeben wird.
if(substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
    {
      ob_start("ob_gzhandler");
    }else{
        ob_start();
    }
  }

// Nach der letzten Ausgabe
   ob_end_flush();
