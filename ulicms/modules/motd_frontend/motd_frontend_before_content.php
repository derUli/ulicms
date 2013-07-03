<?php 
if(!isset($_COOKIE["motd_shown"])){
   setcookie ("motd_shown", "yes", time() + (60 * 60 * 24));
   echo '<div class="motd">'.getconfig("motd").'</div>';
}
?>