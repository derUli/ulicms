
<?php
if (defined ("_SECURITY")){
     $acl = new ACL ();
     if ($acl -> hasPermission ("motd")){
        
         $languages = getAllLanguages ();
        
         if (isset ($_POST ["submit"])){
             for($i = 0; $i < count ($languages); $i ++){
                
                 $lang = $languages [$i];
                 if (isset ($_POST ["motd_" . $lang])){
                     $page = db_escape ($_POST ["motd_" . $lang]);
                     setconfig ("motd_" . $lang, $page);
                     if ($lang == getconfig ("default_language")){
                         setconfig ("motd", $page);
                         }
                     }
                 }
             }
        
         $motd = array ();
        
         for($i = 0; $i < count ($languages); $i ++){
             $lang = $languages [$i];
             $motd [$lang] = getconfig ("motd_" . $lang);
            
             if (! $motd [$lang])
                 $motd [$lang] = getconfig ("motd");
             }
        
         ?>
<h1>
<?php
        
         translate("motd");
         ?>
</h1>
<form action="index.php?action=motd"
	id="motd_settings" method="post">
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
        
         translate("motd");
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
			<td><textarea name="motd_<?php
            
             echo $lang;
             ?>" rows=10><?php echo stringHelper :: real_htmlspecialchars ($motd [$lang]); ?></textarea></td>
			<?php
             }
         ?>
		
		
		
		
		
		<tr>
			<td></td>
			<td style="text-align: center"><input name="submit" type="submit" value="<?php
        
         echo TRANSLATION_SAVE_CHANGES;
         ?>"></td>
	
	</table>
</form>

<script type="text/javascript">
$("#motd_settings").ajaxForm({beforeSubmit: function(e){
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