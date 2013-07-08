<?php 
// Nach einem erfolgreichen login werden die fehlerhaften Loginversuche der IP wieder gelöscht.
db_query("DELETE FROM ".tbname("failed_logins")." WHERE ip='".$_SERVER["REMOTE_ADDR"]."'");
?>