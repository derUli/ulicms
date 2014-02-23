<?php
$acl = new ACL();
if($acl -> hasPermission("motd")){
     ?>
<div>
<h2>Message Of The Day</h2>
<?php
     if(isset($_POST["motd"])){
        
         $motd = strip_tags($_POST["motd"], getconfig("allowed_html"));
         $motd = db_escape($motd);
         setconfig("motd", $motd);
        
        
        ?>
<p>Die Message Of the Day wurde geändert.</p>
<?php }
    ?>

<form id="motd_form" action="index.php?action=motd" method="post">
<textarea name="motd" cols=60 rows=15><?php echo htmlspecialchars(getconfig("motd"));
     ?></textarea>
<br>
<br>
<input type="submit" name="motd_submit" value="MOTD Ändern">
<p><strong>Erlaubte HTML-Tags:</strong><br/>
<?php echo htmlspecialchars(
        getconfig("allowed_html"))?></p>
<?php
     if(getconfig("override_shortcuts") == "on" || getconfig("override_shortcuts") == "backend"){
         ?>
<script type="text/javascript" src="scripts/ctrl-s-submit.js">
</script>
<?php }
     ?>
</form>
</div>
<script type="text/javascript">
$("#motd_form").ajaxForm({beforeSubmit: function(e){
  $("#message").html("");
  $("#loading").show();
  }, 
  success:function(e){
  $("#loading").hide();  
  $("#message").html("<span style=\"color:green;\">Die Einstellungen wurden gespeichert.</span>");
  }
  

}); 

</script>

<?php
    
     }else{
     noperms();
     }

?>