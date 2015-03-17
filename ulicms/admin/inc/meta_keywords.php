
<?php if(defined("_SECURITY")){
     $acl = new ACL();
     if($acl -> hasPermission("settings_simple")){
        
        
         $languages = getAllLanguages();
        
         if(isset($_POST["submit"])){
             for($i = 0; $i < count($languages); $i++){
                
                 $lang = $languages[$i];
                 if(isset($_POST["meta_keywords_" . $lang])){
                     $page = db_escape($_POST["meta_keywords_" . $lang]);
                     setconfig("meta_keywords_" . $lang, $page);
                     if($lang == getconfig("default_language")){
                         setconfig("meta_keywords", $page);
                         }
                     }
                 }
             }
        
        
        
         $meta_keywordss = array();
        
         for($i = 0; $i < count($languages); $i++){
             $lang = $languages[$i];
             $meta_keywordss[$lang] = getconfig("meta_keywords_" . $lang);
            
             if(!$meta_keywordss[$lang])
                 $meta_keywordss[$lang] = getconfig("meta_keywords");
            
            
             }
        
        
         ?>
<h1><?php echo TRANSLATION_META_KEYWORDS;
         ?></h1>
<form action="index.php?action=meta_keywords" id="meta_keywords" method="post">
<?php csrf_token_html();
        ?>
<table border=0>
<tr>
<td style="min-width:100px;"><strong><?php echo TRANSLATION_LANGUAGE;
         ?></strong></td>
<td><strong><?php echo TRANSLATION_META_KEYWORDS;
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
<input name="meta_keywords_<?php echo $lang;
             ?>" style="width:400px" value="<?php echo stringHelper :: real_htmlspecialchars($meta_keywordss[$lang]);
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
$("#meta_keywords_settings").ajaxForm({beforeSubmit: function(e){
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