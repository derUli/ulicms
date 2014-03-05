
<?php if(defined("_SECURITY")){
     $acl = new ACL();
     if($acl -> hasPermission("settings_simple")){
        
        
         $languages = getAllLanguages();
        
         if(isset($_POST["submit"])){
             for($i = 0; $i < count($languages); $i++){
                
                 $lang = $languages[$i];
                 if(isset($_POST["frontpage_" . $lang])){
                     $page = db_escape($_POST["frontpage_" . $lang]);
                     setconfig("frontpage_" . $lang, $page);
                     if($lang == getconfig("default_language")){
                         setconfig("frontpage", $page);
                         }
                     }
                 }
             }
        
        
        
         $frontpages = array();
        
         for($i = 0; $i < count($languages); $i++){
             $lang = $languages[$i];
             $frontpages[$lang] = getconfig("frontpage_" . $lang);
            
             if(!$frontpages[$lang])
                 $frontpages[$lang] = getconfig($frontpage);
            
            
             }
        
         $pages = getAllSystemNames();
        
        ?>
<h1>Startseite</h1>
<form action="index.php?action=frontpage_settings" id="frontpage_settings" method="post">
<table border=0>
<tr>
<td style="min-width:100px;"><strong>Sprache</strong></td>
<td><strong>Startseite</strong></td>
</tr>
<?php
        for($n = 0; $n < count($languages); $n++){
             $lang = $languages[$n];
            ?>
<tr>
<td><?php echo $lang;
            ?></td>
<td>
<select name="frontpage_<?php echo $lang;
            ?>" size=1 style="width:400px">
<?php for($i = 0; $i < count($pages);$i++){
                 if($pages[$i] == $frontpages[$lang]){
                     echo "<option value='" . $pages[$i] . "' selected='selected'>" . $pages[$i] . "</option>";
                     }else{
                     echo "<option value='" . $pages[$i] . "'>" . $pages[$i] . "</option>";
                     }
                
                 }
             ?>
</select>

</td>
<?php }
        ?>
<tr>
<td>
</td>
<td style="text-align:center">
<input type="submit" name="submit" value="Einstellungen Speichern">
</td>
</table>
</form>

<script type="text/javascript">
$("#frontpage_settings").ajaxForm({beforeSubmit: function(e){
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