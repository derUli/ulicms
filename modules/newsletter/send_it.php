<?php 


if(defined(MODULE_ADMIN_REQUIRED_PERMISSION)){
  if($_SESSION["group"] < MODULE_ADMIN_REQUIRED_PERMISSION){
    die("Fuck you!");
  }

}

if(isset($_SESSION["newsletter_data"])){
   @ignore_user_abort(1); // run script in background 
   @set_time_limit(0); // run script forever 
   // Start send loop
   send_loop();
}


?>