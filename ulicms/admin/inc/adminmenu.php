<?php
if (defined ( "_SECURITY" )) {
	$acl = new ACL ();
	$modules = getAllModules ();
	$modules_with_admin_page = Array ();
	for($i = 0; $i < count ( $modules ); $i ++) {
		if (file_exists ( getModuleAdminFilePath ( $modules [$i] ) ) or file_exists ( getModuleAdminFilePath2 ( $modules [$i] ) )) {
			$admin_permission = getModuleMeta ( $modules [$i], "admin_permission" );
			$allowed = true;
			if (isNotNullOrEmpty ( $admin_permission )) {
				$allowed = $acl->hasPermission ( $admin_permission );
			}
			if ($allowed) {
				array_push ( $modules_with_admin_page, $modules [$i] );
			}
		}
	}
	
	$theme = Settings::get ( "theme" );
	$theme_dir = getTemplateDirPath ( $theme );
	$acl = new ACL ();
	
	?>
<div style="float: left">
	<h2>
		UliCMS <a href="../">[<?php echo Settings::get("homepage_title")?>]</a>
	</h2>
</div>


<div style="margin-right: 10px; margin-top: 30px; float: right">
	<img id="loading" src="gfx/loading.gif" alt="Bitte warten..."
		style="display: none;">
</div>
<div id="message"
	style="margin-top: 30px; text-align: center; margin-right: 10px; float: right;"></div>
<div class="clear"></div>
<div class="navbar_top">
	<ul class="menu">
		<li><a href='?action=home'><?php translate("welcome");?>
		</a>
			<ul>
				<li><a
					href="?action=admin_edit&admin=<?php echo $_SESSION["login_id"]?>"><?php translate("edit_profile");?>
				</a></li>
			</ul></li>
			<?php
	
	if ($acl->hasPermission ( "banners" ) or $acl->hasPermission ( "pages" ) or $acl->hasPermission ( "categories" ) or $acl->hasPermission ( "forms" )) {
		?>
		<li><a href='?action=contents'><?php translate("contents");?>
		</a>
			<ul>
			<?php
		
		if ($acl->hasPermission ( "pages" )) {
			?>
				<li><a href='?action=pages'><?php translate("pages");?></a></li>
				<?php
		}
		?>
<?php

		if ($acl->hasPermission ( "forms" )) {
			?>
				<li><a href='?action=forms'><?php
			
			translate ( "forms" );
			?></a></li>
				<?php
		}
		?>
			<?php
		
		if ($acl->hasPermission ( "banners" )) {
			?>
				<li><a href='?action=banner'><?php translate("advertisements");?></a></li>

				<?php
		}
		?>

			<?php
		
		if ($acl->hasPermission ( "categories" )) {
			?>
				<li><a href='?action=categories'><?php translate("categories");?></a></li>
				<?php
		}
		?>			<?php
		
		if ($acl->hasPermission ( "export" )) {
			?>
				<li><a href='?action=export'><?php translate("export");?></a></li>
				<?php
		}
		?>

			</ul></li>
			<?php
	}
	?>
			<?php
	
	if ($acl->hasPermission ( "images" ) or $acl->hasPermission ( "videos" ) or $acl->hasPermission ( "audio" ) or $acl->hasPermission ( "files" )) {
		?>
		<li><a href="?action=media"><?php translate("media");?>
		</a>
			<ul>
			<?php
		
		if ($acl->hasPermission ( "images" )) {
			?>
				<li><a href="?action=images"><?php translate("images");?></a></li>
				<?php
		}
		?>

			<?php
		
		if ($acl->hasPermission ( "files" )) {
			?>
				<li><a href="?action=files"><?php translate("files");?></a></li>
				<?php
		}
		?>


			<?php
		
		if ($acl->hasPermission ( "videos" )) {
			?>
				<li><a href="?action=videos"><?php translate("videos");?></a></li>
				<?php
		}
		?>
			<?php
		
		if ($acl->hasPermission ( "audio" )) {
			?>
				<li><a href="?action=audio"><?php translate("audio");?></a></li>
				<?php
		}
		?>
			</ul></li>

			<?php
	}
	?>
			<?php
	
	if ($acl->hasPermission ( "users" )) {
		?>
		<li><a href="?action=admins"><?php translate("users");?>
		</a></li>
		<?php
	}
	?>
			<?php
	
	if (is_admin () or $acl->hasPermission ( "groups" )) {
		?>
		<li><a href="?action=groups"><?php translate("groups");?>
		</a></li>
		<?php
	}
	?>
			<?php
	
	if ($acl->hasPermission ( "list_packages" )) {
		?>
		<li><a href="?action=modules"><?php translate("packages")?>
		</a> <?php
		
		if (count ( $modules_with_admin_page ) > 0) {
			?>
			<ul>
			<?php
			
			for($n = 0; $n < count ( $modules_with_admin_page ); $n ++) {
				
				?>
				<li><a
					href="?action=module_settings&module=<?php echo $modules_with_admin_page[$n]?>"><?php echo getModuleName($modules_with_admin_page[$n])?>
				</a></li>
				<?php
			}
			?>
			</ul> <?php
		}
		?>
		</li>
		<?php
	}
	?>
			<?php
	
	if (file_exists ( ULICMS_ROOT . DIRECTORY_SEPERATOR . "update.php" ) and ($acl->hasPermission ( "update_system" ) or is_admin ())) {
		?>


		<li><a href="?action=system_update"><?php translate("update");?>
		</a></li>
		<?php
	}
	?>
			<?php
	
	if ($acl->hasPermission ( "settings_simple" ) or $acl->hasPermission ( "design" ) or $acl->hasPermission ( "spam_filter" ) or $acl->hasPermission ( "cache" ) or $acl->hasPermission ( "motd" ) or $acl->hasPermission ( "pkg_settings" ) or $acl->hasPermission ( "logo" ) or $acl->hasPermission ( "languages" ) or $acl->hasPermission ( "other" )) {
		?>
		<li><a href="?action=settings_categories"><?php translate("settings");?>
		</a>

			<ul>
			<?php
		
		if ($acl->hasPermission ( "settings_simple" )) {
			?>
				<li><a href="?action=settings_simple"><?php translate("general_settings");?></a></li>
				<?php
		}
		?>
			<?php
		
		if ($acl->hasPermission ( "design" )) {
			?>
				<li><a href="?action=design"><?php translate("design");?></a></li>
				<?php
		}
		?>
			<?php
		
		if ($acl->hasPermission ( "spam_filter" )) {
			?>
				<li><a href="?action=spam_filter"><?php translate("spamfilter");?></a></li>
				<?php
		}
		?>
			<?php
		
		if ($acl->hasPermission ( "cache" )) {
			?>
				<li><a id="clear_cache" href="?action=cache&clear_cache=yes"><?php translate("clear_cache");?></a></li>
				<?php
		}
		?>
			<?php
		
		if ($acl->hasPermission ( "motd" )) {
			?>
				<li><a href="?action=motd"><?php translate("motd");?></a></li>
				<?php
		}
		?>
			<?php
		
		if ($acl->hasPermission ( "pkg_settings" )) {
			?>
				<li><a href="?action=pkg_settings"><?php translate("package_source");?></a></li>
				<?php
		}
		?>
			<?php
		
		if ($acl->hasPermission ( "logo" )) {
			?>
				<li><a href="?action=logo_upload"><?php translate("logo");?></a></li>
				<?php
		}
		?>
			<?php
		
		if ($acl->hasPermission ( "languages" )) {
			?>
				<li><a href="?action=languages"><?php translate("languages");?></a></li>
				<?php
		}
		?>

			<?php
		
		if ($acl->hasPermission ( "other" )) {
			?>
				<li><a href="?action=other_settings"><?php translate("other");?></a></li>
				<?php
		}
		?>
			</ul> <?php
	}
	?> <?php
	
	if ($acl->hasPermission ( "info" )) {
		?>
</li>
		<li><a href="?action=info"><?php translate("info");?>
		</a>
			<ul>
				<li><a href="http://www.ulicms.de/" target="_blank">UliCMS Portal</a>
				</li>
				<li><a href="http://www.ulicms.de/forum.html" target="_blank">Community</a>
				</li>
				<li><a href="license.html" target="_blank"><?php translate("license");?>
				</a></li>
				<li><a href="http://www.ulicms.de/?seite=kontakt" target="_blank">Feedback</a>
				</li>
			</ul> <?php
		
		add_hook ( "admin_menu_item" );
		?> <?php
	}
	?></li>
		<li><a href="?action=destroy"
			onclick="return confirm('<?php
	
	translate ( "logout" );
	?>?')"><?php translate("logout");?>
		</a></li>
	</ul>
	<script type="text/javascript">

$('#clear_cache')
   .click(function (event) {
       $("#message").html("")
       $("#loading").show();

       $.ajax({
       url: "index.php?action=cache&clear_cache=yes",
       success: function(evt){
       $("#loading").hide();
       $("#message").html("<span style=\"color:green\"><?php translate("cache_was_cleared");?></span>");

       },
       error: function(evt){

          $("#loading").hide();
          alert("AJAX Error");
       },
       dataType: "html"
});


       event.preventDefault();
       event.stopPropagation();
});
</script>
</div>
<div class="clear"></div>
<div id="pbody">
	<div id="site-switcher-container">
		<strong><?php translate("website");?></strong><br />
		<form action="index.php" id="site-form" method="get">
			<input name="action" type="hidden"
				value="<?php echo htmlspecialchars($_GET["action"]);?>"> <select
				name="site-id" onchange="$('form#site-form').submit();">
				<option value="default"><?php translate("default_website");?></option>
			</select>
		</form>
	</div>
	<?php
}
?>