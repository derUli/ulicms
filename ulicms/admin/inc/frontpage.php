<?php
if (defined ("_SECURITY")){
     $acl = new ACL ();
     if ($acl -> hasPermission ("settings_simple")){
        
         $languages = getAllLanguages ();
        
         if (isset ($_POST ["submit"])){
             for($i = 0; $i < count ($languages); $i ++){
                
                 $lang = $languages [$i];
                 if (isset ($_POST ["frontpage_" . $lang])){
                     $page = db_escape ($_POST ["frontpage_" . $lang]);
                     setconfig ("frontpage_" . $lang, $page);
                     if ($lang == getconfig ("default_language")){
                         setconfig ("frontpage", $page);
                         }
                     }
                 }
             }
        
         $frontpages = array ();
        
         for($i = 0; $i < count ($languages); $i ++){
             $lang = $languages [$i];
             $frontpages [$lang] = getconfig ("frontpage_" . $lang);
            
             if (! $frontpages [$lang])
                 $frontpages [$lang] = getconfig ("frontpage");
             }
        
         ?>
<h1>
<?php
        
         echo TRANSLATION_FRONTPAGE;
         ?>
</h1>
<form action="index.php?action=frontpage_settings"
	id="frontpage_settings" method="post">
	<?php
        
         csrf_token_html ();
         ?>
	<table border=0>
		<tr>
			<td style="min-width: 100px;"><strong><?php
        
         echo TRANSLATION_LANGUAGE;
         ?>
			</strong></td>
			<td><strong><?php
        
         echo TRANSLATION_FRONTPAGE;
         ?>
			</strong></td>
		</tr>
		<?php
         for($n = 0; $n < count ($languages); $n ++){
             $lang = $languages [$n];
             ?>
		<tr>
			<td><?php
            
             echo $lang;
             ?></td>
			<td><select name="frontpage_<?php
            
             echo $lang;
             ?>" size=1
				style="width: 400px">
				<?php
            
             $pages = getAllPages ($lang, "title", true);
            
             for($i = 0; $i < count ($pages); $i ++){
                 if ($pages [$i] ["systemname"] == $frontpages [$lang]){
                     echo "<option value='" . $pages [$i] ["systemname"] . "' selected='selected'>" . $pages [$i] ["title"] . " (ID: " . $pages [$i] ["id"] . ")</option>";
                     }else{
                     echo "<option value='" . $pages [$i] ["systemname"] . "'>" . $pages [$i] ["title"] . " (ID: " . $pages [$i] ["id"] . ")</option>";
                     }
                 }
             ?>
			</select></td>
			<?php
             }
         ?>
		
		
		
		
		
		<tr>
			<td></td>
			<td style="text-align: center"><input type="submit" name="submit"
				value="<?php
        
         echo TRANSLATION_SAVE_CHANGES;
         ?>"></td>
	
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

<?php
         }else{
         noperms ();
         }
    
    }
?>