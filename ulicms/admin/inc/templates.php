<?php if(defined("_SECURITY")){
     $acl = new ACL();
     if($acl -> hasPermission("templates")){
        
         $theme = getconfig("theme");
         ?>


<h2><?php echo TRANSLATION_TEMPLATES;
         ?></h2>
<?php
        
         if(!empty($_GET["save"])){
             if($_GET["save"] == "true"){
                 echo "<p>Die Template wurde gespeichert.</p>";
                 }else{
                 echo "<p>Die Template konnte nicht gespeichert werden. Möglicherweise ein Problem mit den Dateirechten auf dem Server?</p>";
                 }
             }
        else if(empty($_GET["edit"])){
             ?>

<p><?php echo ULICMS_TEMPLATE_INFO_TEXT;
             ?></p>
<strong><?php echo TRANSLATION_PLEASE_SELECT_TEMPLATE;
             ?></strong><br/>
<p><a href="index.php?action=templates&edit=oben.php"><?php echo TRANSLATION_TOP;
             ?></a></p>
<p><a href="index.php?action=templates&edit=unten.php"><?php echo TRANSLATION_BOTTOM;
             ?></a></p>
<p><a href="index.php?action=templates&edit=maintenance.php"><?php echo TRANSLATION_MAINTENANCE_PAGE;
             ?></a></p>
<p><a href="index.php?action=templates&edit=style.css"><?php echo TRANSLATION_CSS;
             ?></a></p>

<?php
             if(file_exists(getTemplateDirPath($theme) . "403.php")){
                 ?>
<p><a href="index.php?action=templates&edit=403.php">403 Fehlerseite</a></p>
<?php
                 }
             ?>

<?php
             if(file_exists(getTemplateDirPath($theme) . "404.php")){
                 ?>
<p><a href="index.php?action=templates&edit=404.php">404 Fehlerseite</a></p>
<?php
                 }
             ?>

 <?php
             if(file_exists(getTemplateDirPath($theme) . "functions.php")){
                 ?>
      <p><a href="index.php?action=templates&edit=functions.php">Functions</a></p>
     <?php
                 }
             ?>


<?php }else if (!empty($_GET["edit"])){
             $edit = basename($_GET["edit"]);
             $template_file = getTemplateDirPath($theme) . $edit;
            
             if(is_file($template_file)){
                
                 if(!is_writable($template_file) && file_exists($template_file)){
                     echo "<p>Die gewählte Template konnte nicht geöffnet werden. Wenn Sie der Inhaber dieser Seite sind, probieren Sie die Datei-Rechte auf dem FTP-Server auf 0777 zu setzen. Wenn nicht, wenden Sie sich bitte an Ihren Administrator.</p>";
                     }else{
                     $template_content = file_get_contents($template_file);
                    
                     ?>
<form id="templateForm" action="index.php?action=templates" method="post">
<style type="text/css">
.CodeMirror {
  border: 1px solid #eee;
  height: auto;
  overflow:hidden;
}
.CodeMirror-scroll {
  overflow-y: hidden;
  overflow-x: auto;
}
</style>
<textarea id="code" name="code" cols=80 rows=20><?php
                     echo htmlspecialchars($template_content);
                     ?></textarea>
 <script type="text/javascript">
      var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
        lineNumbers: true,
        matchBrackets: true,
		
        mode: "<?php switch(file_extension($edit)){
                     case "php":
                         echo "application/x-httpd-php";
                         break;
                         break;
                     case "css":
                         echo "text/css";
                         break;
                     case "txt":
                         echo "application/x-httpd-php";
                         break;
                         }
                     ?>",
		
        indentUnit: 0,
        indentWithTabs: false,
        enterMode: "keep",
        tabMode: "shift"
      });
    </script>
  
    <input type="hidden" name="save_template" value="<?php echo htmlspecialchars($edit);
                     ?>">
    <div class="inPageMessage">
<div id="message_page_edit" class="inPageMessage"></div>
<img class="loading" src="gfx/loading.gif" alt="Wird gespeichert...">
</div>
    <input type="submit" value="<?php echo TRANSLATION_SAVE_CHANGES;
                     ?>">
    
<?php
                     if(getconfig("override_shortcuts") == "on" || getconfig("override_shortcuts") == "backend"){
                     ?>
<script type="text/javascript" src="scripts/ctrl-s-submit.js">
</script>
<?php }
                 ?>
</form>
<script type="text/javascript">
$("#templateForm").ajaxForm({beforeSubmit: function(e){
  $("#message_page_edit").html("");
  $("#message_page_edit").hide();
  $(".loading").show();
  }, 
  success:function(e){
  $(".loading").hide();  
  $("#message_page_edit").html("<span style=\"color:green;\"><?php echo TRANSLATION_CHANGES_WAS_SAVED;
                 ?></span>");
  $("#message_page_edit").show();
  }
  
}); 

</script>
<?php
                
                
                
                 }
            
             }
        
         ?>




<?php }
     ?>

<?php
     }
else{
     noperms();
     }

 ?>




<?php }
?>
