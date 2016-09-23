<?php
if (defined ( "_SECURITY" )) {
	$acl = new ACL ();
	if ($acl->hasPermission ( "banners" )) {
		$banner = db_escape ( $_GET ["banner"] );
		$query = db_query ( "SELECT * FROM " . tbname ( "banner" ) . " WHERE id='$banner'" );
		while ( $row = db_fetch_object ( $query ) ) {
			?>

<form action="index.php?action=banner" method="post">
<?php
			
			csrf_token_html ();
			?>
	<h4><?php translate("preview");?></h4>
	
	<?php
			
			if ($row->type == "gif") {
				?>
				<p>
		<a
			href="<?php
				
				Template::escape ( $row->link_url );
				?>"
			target="_blank"><img
			src="<?php
				
				Template::escape ( $row->image_url );
				?>"
			title="<?php
				
				Template::escape ( $row->name );
				?>"
			alt="<?php
				
				Template::escape ( $row->name );
				?>"
			border=0> </a>
	</p>

	<?php
			} else {
				echo $row->html;
			}
			?>
	


	<input type="hidden" name="edit_banner" value="edit_banner"> <input
		type="hidden" name="id" value="<?php
			
			echo $row->id;
			?>">
	<p>
		<input type="radio"
			<?php
			if ($row->type == "gif") {
				echo 'checked="checked"';
			}
			;
			?>
			id="radio_gif" name="type" value="gif"
			onclick="$('#type_gif').slideDown();$('#type_html').slideUp();"><label
			for="radio_gif"><?php translate("gif_banner");?></label>
	</p>
	<fieldset id="type_gif" style="<?php
			
			if ($row->type != "gif") {
				echo "display:none";
			}
			?>">

		<strong><?php
			
			translate ( "bannertext" );
			?></strong><br /> <input type="text" name="banner_name"
			value="<?php
			
			Template::escape ( $row->name );
			?>"> <br /> <br /> <strong><?php
			
			translate ( "IMAGE_URL" );
			?></strong><br /> <input type="text" name="image_url"
			value="<?php
			
			Template::escape ( $row->image_url );
			?>"> <br /> <br /> <strong><?php translate("link_url");?></strong><br />
		<input type="text" name="link_url"
			value="<?php
			
			Template::escape ( $row->link_url );
			?>">
	</fieldset>
	<br /> <input type="radio"
		<?php
			if ($row->type == "html") {
				echo 'checked="checked"';
			}
			?>
		id="radio_html" name="type" value="html"
		onclick="$('#type_html').slideDown();$('#type_gif').slideUp();"><label
		for="radio_html">HTML</label>

	<fieldset id="type_html" style="<?php
			
			if ($row->type != "html") {
				echo "display:none";
			}
			?>">
		<textarea name="html" cols=40 rows=10><?php
			
			echo htmlspecialchars ( $row->html );
			?></textarea>
	</fieldset>

	<br /> <strong><?php translate("language");?></strong><br /> <select
		name="language">
	<?php
			$languages = getAllLanguages ();
			
			$page_language = $row->language;
			
			if ($page_language === "all") {
				echo "<option value='all' selected='selected'>" . get_translation ( "every" ) . "</option>";
			} else {
				echo "<option value='all'>" . get_translation ( "every" ) . "</option>";
			}
			
			for($j = 0; $j < count ( $languages ); $j ++) {
				if ($languages [$j] === $page_language) {
					echo "<option value='" . $languages [$j] . "' selected>" . getLanguageNameByCode ( $languages [$j] ) . "</option>";
				} else {
					echo "<option value='" . $languages [$j] . "'>" . getLanguageNameByCode ( $languages [$j] ) . "</option>";
				}
			}
			
			$pages = getAllPages ( $page_language, "title" );
			?>
	</select> <br /> <br /> <strong><?php translate("category");?></strong><br />
	<?php
			echo categories::getHTMLSelect ( $row->category );
			?>

	<br /> <br /> <input type="submit"
		value="<?php translate("save_changes");?>">
			<?php
			if (Settings::get ( "override_shortcuts" ) == "on" || Settings::get ( "override_shortcuts" ) == "backend") {
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
	} else {
		noperms ();
	}
	
	?>




	<?php
}
?>
