<?php
if (defined ("_SECURITY")){
     $acl = new ACL ();
     if ($acl -> hasPermission ("logo")){
         ?>
		<?php
        
         if ($_GET ["error"] == "to_big"){
             ?>
<p style="color: red; font-size: 1.2em">
<?php
            
             echo TRANSLATION_UPLOADED_IMAGE_TO_BIG;
             ?></p>
<?php
             }
         ?>
<p>
<?php
        
         echo TRANSLATION_LOGO_INFOTEXT;
         ?>
</p>
<form enctype="multipart/form-data"
	action="index.php?action=logo_upload" method="post">
	<?php
        
         csrf_token_html ();
         ?>
	<table border=0 height=250>
		<tr>
			<td><strong><?php
        
         echo TRANSLATION_YOUR_LOGO;
         ?>
			</strong></td>
			<td><?php
        
         $logo_path = "../content/images/" . getconfig ("logo_image");
         if (file_exists ($logo_path) and is_file ($logo_path)){
             echo '<img class="website_logo" src="' . $logo_path . '" alt="' . getconfig ("homepage_title") . '"/>';
             }
         ?>
			</td>
		
		
		<tr>
			<td width=480><strong><?php
        
         echo TRANSLATION_UPLOAD_NEW_LOGO;
         ?>
			</strong></td>
			<td><input name="logo_upload_file" type="file"> <br /></td>
		
		
		<tr>
			<td></td>
			<td style="text-align: center"><input type="submit"
				value="<?php
        
         echo TRANSLATION_UPLOAD;
         ?>"></td>
	
	</table>

	<?php
         if (getconfig ("override_shortcuts") == "on" || getconfig ("override_shortcuts") == "backend"){
             ?>
	<script type="text/javascript" src="scripts/ctrl-s-submit.js">
</script>
<?php
             }
         ?>
</form>

<?php
         }else{
         noperms ();
         }
    }

?>