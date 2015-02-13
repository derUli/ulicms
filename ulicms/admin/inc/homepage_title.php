
<?php if(defined("_SECURITY")){
     $acl = new ACL();
     if($acl -> hasPermission("settings_simple")){
        
        
         $languages = getAllLanguages();
        
         if(isset($_POST["submit"])){
             for($i = 0; $i < count($languages); $i++){
                
                 $lang = $languages[$i];
                 if(isset($_POST["homepage_title_" . $lang])){
                     $page = db_escape($_POST["homepage_title_" . $lang]);
                     setconfig("homepage_title_" . $lang, $page);
                     if($lang == getconfig("default_language")){
                         setconfig("homepage_title", $page);
                         }
                     }
                 }
             }
        
        
        
         $homepage_titles = array();
        
         for($i = 0; $i < count($languages); $i++){
             $lang = $languages[$i];
             $homepage_titles[$lang] = getconfig("homepage_title_" . $lang);
            
             if(!$homepage_titles[$lang])
                 $homepage_titles[$lang] = getconfig("homepage_title");
            
            
             }
        
        
         ?>
<h1><?php echo TRANSLATION_HOMEPAGE_TITLE;
         ?></h1>
<form action="index.php?action=homepage_title" id="homepage_title_settings" method="post">
<table border=0>
<tr>
<td style="min-width:100px;"><strong><?php echo TRANSLATION_LANGUAGE;
        ?></strong></td>
<td><strong><?php echo TRANSLATION_TITLE;
        ?></strong></td>
</tr>
<?php
         for($n = 0; $n < count($languages); $n++){
             $lang = $languages[$n];
             ?>
<tr>
<td><?php echo $lang;
             ?></td>
<td>
<input name="homepage_title_<?php echo $lang;
             ?>" style="width:400px" value="<?php echo stringHelper :: real_htmlspecialchars($homepage_titles[$lang]);
             ?>">
</td>
<?php }
         ?>
<tr>
<td>
</td>
<td style="text-align:center">
<input type="submit" name="submit" value="<?php echo TRANSLATION_SAVE_CHANGES;
        ?>">
</td>
</table>
</form>

<script type="text/javascript">
$("#homepage_title_settings").ajaxForm({beforeSubmit: function(e){
  $("#message").html("");
  $("#loading").show();
  }, 
  success:function(e){
  $("#loading").hide();  
  $("#message").html("<span style=\"color:green;\">Die Einstellungen wurden gespeichert.</span>");
  }
  

}); 

</script>

<?php }else{
         noperms();
         }
    
     }
?>