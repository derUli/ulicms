<h1>Cache</h1>
<?php
$acl = new ACL();
if($acl -> hasPermission("cache")){
     ?>
<?php
     if(isset($_GET["clear_cache"])){
         ?>
<p style="color:green;"><?php echo TRANSLATION_CACHE_WAS_CLEARED;
         ?></p>
<?php }
     ?>
<?php echo TRANSLATION_CACHE_TEXT1;
     ?>
<p><strong>Aktueller Status des Caches:</strong><br/>
<?php if(!getconfig("cache_disabled")){
         ?>
<span style="color:green;">aktiv</span></p>

<?php echo TRANSLATION_CACHE_TEXT3;
         ?>


<form post="index.php" method="get">
<?php csrf_token_html();?>
<input type="hidden" name="action" value="cache"/>
<input type="hidden" name="clear_cache" value="yes"/>
<input type="submit" value="Cache leeren"/>

<?php
         if(getconfig("override_shortcuts") == "on" || getconfig("override_shortcuts") == "backend"){
             ?>
<script type="text/javascript" src="scripts/ctrl-s-submit.js">
</script>
<?php }
         ?>
</form>

<?php }else{
         ?>
<span style="color:red;">deaktiviert</span></p>
<?php echo TRANSLATION_CACHE_TEXT2;
         ?>
<?php }
     ?>

<?php }else{
     noperms();
     }

?>