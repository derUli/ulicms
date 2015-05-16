<?php
if (defined ("_SECURITY")){
     $acl = new ACL ();
     if ($acl -> hasPermission ("banners")){
         $banner = db_escape ($_GET ["banner"]);
         $query = db_query ("SELECT * FROM " . tbname ("banner") . " WHERE id='$banner'");
         while ($row = db_fetch_object ($query)){
             ?>

<form action="index.php?action=banner" method="post">
<?php
            
            csrf_token_html ();
            ?>
	<h4>
	<?php
            
             echo TRANSLATION_PREVIEW;
             ?></h4>
	<p>
	<?php
            
             if ($row -> type == "gif"){
                 ?>
		<a href="<?php
                
                 echo $row -> link_url;
                 ?>" target="_blank"><img
			src="<?php
                
                 echo $row -> image_url;
                 ?>"
			title="<?php
                
                 echo $row -> name;
                 ?>"
			alt="<?php
                
                 echo $row -> name;
                 ?>" border=0> </a>
	</p>

	<?php
                 }else{
                 echo $row -> html;
                 }
             ?>
	</p>


	<input type="hidden" name="edit_banner" value="edit_banner"> <input
		type="hidden" name="id" value="<?php
            
             echo $row -> id;
             ?>">
	<p>
		<input type="radio"
		<?php
             if ($row -> type == "gif"){
                 echo 'checked="checked"';
                 }
             ;
             ?>
			id="radio_gif" name="type" value="gif"
			onclick="$('#type_gif').slideDown();$('#type_html').slideUp();"><label
			for="radio_gif"><?php
            
             echo TRANSLATION_GIF_BANNER;
             ?></label>
	</p>
	<fieldset id="type_gif" style="<?php
            
             if ($row -> type != "gif")
                 echo "display:none";
             ?>">

		<strong><?php
            
             echo TRANSLATION_BANNERTEXT;
             ?></strong><br /> <input type="text" name="banner_name"
			value="<?php
            
             echo $row -> name;
             ?>"> <br /> <br /> <strong><?php
            
             echo TRANSLATION_IMAGE_URL;
             ?></strong><br /> <input type="text" name="image_url"
			value="<?php
            
             echo $row -> image_url;
             ?>"> <br /> <br /> <strong><?php
            
             echo TRANSLATION_LINK_URL;
             ?></strong><br /> <input type="text" name="link_url"
			value="<?php
            
             echo $row -> link_url;
             ?>">
	</fieldset>
	<br /> <input type="radio"
	<?php
             if ($row -> type == "html"){
                 echo 'checked="checked"';
                 }
             ;
             ?>
		id="radio_html" name="type" value="html"
		onclick="$('#type_html').slideDown();$('#type_gif').slideUp();"><label
		for="radio_html">HTML</label>
	</p>
	<fieldset id="type_html" style="<?php
            
             if ($row -> type != "html")
                 echo "display:none";
             ?>">
		<textarea name="html" cols=40 rows=10><?php echo htmlspecialchars ($row -> html);
            ?></textarea>
	</fieldset>

	<br /> <strong><?php
            
             echo TRANSLATION_LANGUAGE;
             ?></strong><br /> <select name="language">
	<?php
             $languages = getAllLanguages ();
            
             $page_language = $row -> language;
            
             if ($page_language === "all"){
                 echo "<option value='all' selected='selected'>" . TRANSLATION_EVERY . "</option>";
                 }else{
                 echo "<option value='all'>" . TRANSLATION_EVERY . "</option>";
                 }
            
             for($j = 0; $j < count ($languages); $j ++){
                 if ($languages [$j] === $page_language){
                     echo "<option value='" . $languages [$j] . "' selected>" . getLanguageNameByCode ($languages [$j]) . "</option>";
                     }else{
                     echo "<option value='" . $languages [$j] . "'>" . getLanguageNameByCode ($languages [$j]) . "</option>";
                     }
                 }
            
             $pages = getAllPages ($page_language, "title");
             ?>
	</select> <br /> <br /> <strong><?php
            
             echo TRANSLATION_CATEGORY;
             ?></strong><br />
	<?php
             echo categories :: getHTMLSelect ($row -> category);
             ?>

	<br /> <br /> <input type="submit"
		value="<?php
            
             echo TRANSLATION_SAVE_CHANGES;
             ?>">
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
             break;
             }
         ?>
		<?php
         }else{
         noperms ();
         }
    
     ?>




	<?php
    
    }
?>
