
<?php if(defined("_SECURITY")){
     $acl = new ACL();
     if($acl -> hasPermission("settings_simple")){
        
        
         $languages = getAllLanguages();
        
         if(isset($_POST["submit"])){
             for($i = 0; $i < count($languages); $i++){
                
                 $lang = $languages[$i];
                 if(isset($_POST["meta_description_" . $lang])){
                     $page = db_escape($_POST["meta_description_" . $lang]);
                     setconfig("meta_description_" . $lang, $page);
                     if($lang == getconfig("default_language")){
                         setconfig("meta_description", $page);
                         }
                     }
                 }
             }
        
        
        
         $meta_descriptions = array();
        
         for($i = 0; $i < count($languages); $i++){
             $lang = $languages[$i];
             $meta_descriptions[$lang] = getconfig("meta_description_" . $lang);
            
             if(!$meta_descriptions[$lang])
                 $meta_descriptions[$lang] = getconfig("meta_description");
            
            
             }
        
        
         ?>
<h1><?php echo TRANSLATION_META_DESCRIPTION;
         ?></h1>
<form action="index.php?action=meta_description" id="meta_description" method="post">
<?php csrf_token_html();
        ?>
<table border=0>
<tr>
<td style="min-width:100px;"><strong><?php echo TRANSLATION_LANGUAGE;
         ?></strong></td>
<td><strong><?php echo TRANSLATION_META_DESCRIPTION;
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
<input name="meta_description_<?php echo $lang;
             ?>" style="width:400px" value="<?php echo stringHelper :: real_htmlspecialchars($meta_descriptions[$lang]);
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
$("#meta_description_settings").ajaxForm({beforeSubmit: function(e){
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