<?php
if (defined ( "_SECURITY" )) {
	$modules = getAllModules ();
	$modules_with_admin_page = Array ();
	for($i = 0; $i < count ( $modules ); $i ++) {
		if (file_exists ( getModuleAdminFilePath ( $modules [$i] ) ))
			array_push ( $modules_with_admin_page, $modules [$i] );
	}
	
	$theme = getconfig ( "theme" );
	$theme_dir = getTemplateDirPath ( $theme );
	$acl = new ACL ();
	
	?>
<div style="float: left">
	<h2>
		UliCMS <a href="../">[<?php echo getconfig("homepage_title")?>]</a>
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
		<li><a href='?action=home'><?php
	
	echo TRANSLATION_WELCOME;
	?>
		</a>
			<ul>
				<li><a
					href="?action=admin_edit&admin=<?php echo $_SESSION["login_id"]?>"><?php
	
	echo TRANSLATION_EDIT_PROFILE;
	?>
				</a></li>
			</ul></li>
			<?php
	
	if ($acl->hasPermission ( "banners" ) or $acl->hasPermission ( "pages" ) or $acl->hasPermission ( "categories" )) {
		?>
		<li><a href='?action=contents'><?php
		
		echo TRANSLATION_CONTENTS;
		?>
		</a>
			<ul>
			<?php
		
		if ($acl->hasPermission ( "pages" )) {
			?>
				<li><a href='?action=pages'><?php
			
			echo TRANSLATION_PAGES;
			?></a></li>
				<?php
		}
		?>

			<?php
		
		if ($acl->hasPermission ( "banners" )) {
			?>
				<li><a href='?action=banner'><?php
			
			echo TRANSLATION_ADVERTISEMENTS;
			?></a></li>

				<?php
		}
		?>

			<?php
		
		if ($acl->hasPermission ( "categories" )) {
			?>
				<li><a href='?action=categories'><?php
			
			echo TRANSLATION_CATEGORIES;
			?></a></li>
				<?php
		}
		?>

			<?php
		
		if ($acl->hasPermission ( "import" )) {
			?>
				<!--
            <li>
              <a href='?action=import'><?php
			
			echo TRANSLATION_IMPORT;
			?></a>
            </li> -->
			<?php
		}
		?>

			<?php
		
		if ($acl->hasPermission ( "export" )) {
			?>
				<li><a href='?action=export'><?php
			
			echo TRANSLATION_EXPORT;
			?></a></li>
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
		<li><a href="?action=media"><?php
		
		echo TRANSLATION_MEDIA;
		?>
		</a>
			<ul>
			<?php
		
		if ($acl->hasPermission ( "images" )) {
			?>
				<li><a href="?action=images"><?php
			
			echo TRANSLATION_IMAGES;
			?></a></li>
				<?php
		}
		?>

			<?php
		
		if ($acl->hasPermission ( "files" )) {
			?>
				<li><a href="?action=files"><?php
			
			echo TRANSLATION_FILES;
			?></a></li>
				<?php
		}
		?>


			<?php
		
		if ($acl->hasPermission ( "videos" )) {
			?>
				<li><a href="?action=videos"><?php
			
			echo TRANSLATION_VIDEOS;
			?></a></li>
				<?php
		}
		?>
			<?php
		
		if ($acl->hasPermission ( "audio" )) {
			?>
				<li><a href="?action=audio"><?php
			
			echo TRANSLATION_AUDIO;
			?></a></li>
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
		<li><a href="?action=admins"><?php
		
		echo TRANSLATION_USERS;
		?>
		</a></li>
		<?php
	}
	?>
			<?php
	
	if (is_admin () or $acl->hasPermission ( "groups" )) {
		?>
		<li><a href="?action=groups"><?php
		
		echo TRANSLATION_GROUPS;
		?>
		</a></li>
		<?php
	}
	?>
			<?php
	
	if ($acl->hasPermission ( "templates" )) {
		?>
		<li><a href="?action=templates"><?php
		
		echo TRANSLATION_TEMPLATES;
		?>
		</a>
			<ul>

			<?php
		if (file_exists ( getTemplateDirPath ( $theme ) . "oben.php" )) {
			?>
				<li><a href="?action=templates&edit=oben.php"><?php
			
			echo TRANSLATION_TOP;
			?></a></li>

				<?php
		
}
		?>


				<?php
		if (file_exists ( getTemplateDirPath ( $theme ) . "unten.php" )) {
			?>
				<li><a href="?action=templates&edit=unten.php"><?php
			
			echo TRANSLATION_BOTTOM;
			?></a></li>


				<?php
		
}
		?>
				<?php
		if (file_exists ( getTemplateDirPath ( $theme ) . "maintenance.php" )) {
			?>
				<li><a href="?action=templates&edit=maintenance.php"><?php
			
			echo TRANSLATION_MAINTENANCE_PAGE;
			?></a></li>

				<?php
		
}
		?>

				<?php
		if (file_exists ( getTemplateDirPath ( $theme ) . "style.css" )) {
			?>
				<li><a href="?action=templates&edit=style.css"><?php
			
			echo TRANSLATION_CSS;
			?></a></li>

				<?php
		
}
		?>
				<?php
		if (file_exists ( $theme_dir . "403.php" )) {
			?>
				<li><a href="index.php?action=templates&edit=403.php">403</a></li>
				<?php
		}
		?>

				<?php
		if (file_exists ( $theme_dir . "404.php" )) {
			?>
				<li><a href="index.php?action=templates&edit=404.php">404</a></li>
				<?php
		}
		?>

				<?php
		if (file_exists ( $theme_dir . "functions.php" )) {
			?>
				<li><a href="index.php?action=templates&edit=functions.php"><?php
			
			echo TRANSLATION_ADDITIONAL_FUNCTIONS;
			?></a></li>
				<?php
		}
		?>

			</ul></li>

			<?php
	}
	?>
			<?php
	
	if ($acl->hasPermission ( "list_packages" )) {
		?>
		<li><a href="?action=modules"><?php
		
		echo TRANSLATION_PACKAGES;
		?>
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


		<li><a href="?action=system_update"><?php
		
		echo TRANSLATION_UPDATE;
		?>
		</a></li>
		<?php
	}
	?>
			<?php
	
	if ($acl->hasPermission ( "settings_simple" ) or $acl->hasPermission ( "design" ) or $acl->hasPermission ( "spam_filter" ) or $acl->hasPermission ( "cache" ) or $acl->hasPermission ( "motd" ) or $acl->hasPermission ( "pkg_settings" ) or $acl->hasPermission ( "logo" ) or $acl->hasPermission ( "languages" ) or $acl->hasPermission ( "other" )) {
		?>
		<li><a href="?action=settings_categories"><?php
		
		echo TRANSLATION_SETTINGS;
		?>
		</a>

			<ul>
			<?php
		
		if ($acl->hasPermission ( "settings_simple" )) {
			?>
				<li><a href="?action=settings_simple"><?php
			
			echo TRANSLATION_GENERAL_SETTINGS;
			?></a></li>
				<?php
		}
		?>
			<?php
		
		if ($acl->hasPermission ( "design" )) {
			?>
				<li><a href="?action=design"><?php
			
			echo TRANSLATION_DESIGN;
			?></a></li>
				<?php
		}
		?>
			<?php
		
		if ($acl->hasPermission ( "spam_filter" )) {
			?>
				<li><a href="?action=spam_filter"><?php
			
			echo TRANSLATION_SPAMFILTER;
			?></a></li>
				<?php
		}
		?>
			<?php
		
		if ($acl->hasPermission ( "cache" )) {
			?>
				<li><a id="clear_cache" href="?action=cache&clear_cache=yes"><?php
			
			echo TRANSLATION_CLEAR_CACHE;
			?></a></li>
				<?php
		}
		?>
			<?php
		
		if ($acl->hasPermission ( "motd" )) {
			?>
				<li><a href="?action=motd"><?php
			
			echo TRANSLATION_MOTD;
			?></a></a></li>
				<?php
		}
		?>
			<?php
		
		if ($acl->hasPermission ( "pkg_settings" )) {
			?>
				<li><a href="?action=pkg_settings"><?php
			
			echo TRANSLATION_PACKAGE_SOURCE;
			?></a></li>
				<?php
		}
		?>
			<?php
		
		if ($acl->hasPermission ( "logo" )) {
			?>
				<li><a href="?action=logo_upload"><?php
			
			echo TRANSLATION_LOGO;
			?></a></li>
				<?php
		}
		?>
			<?php
		
		if ($acl->hasPermission ( "languages" )) {
			?>
				<li><a href="?action=languages"><?php
			
			echo TRANSLATION_LANGUAGES;
			?></a></li>
				<?php
		}
		?>

			<?php
		
		if ($acl->hasPermission ( "other" )) {
			?>
				<li><a href="?action=other_settings"><?php
			
			echo TRANSLATION_OTHER;
			?></a></li>
				<?php
		}
		?>
			</ul> <?php
	}
	?> <?php
	
	if ($acl->hasPermission ( "info" )) {
		?>
		
		
		
		<li><a href="?action=info"><?php
		
		echo TRANSLATION_INFO;
		?>
		</a>
			<ul>
				<li><a href="http://www.ulicms.de/" target="_blank">UliCMS Portal</a>
				</li>
				<li><a href="http://www.ulicms.de/forum.html" target="_blank">Community</a>
				</li>
				<li><a href="license.html" target="_blank"><?php
		
		echo TRANSLATION_LICENSE;
		?>
				</a></li>
				<li><a href="http://www.ulicms.de/?seite=kontakt" target="_blank">Feedback</a>
				</li>
			</ul> <?php
		
		add_hook ( "admin_menu_item" );
		?> <?php
	}
	?>
		
		
		
		<li><a href="?action=destroy"
			onclick="return confirm('<?php
	
	translate ( "logout" );
	?>?')"><?php
	
	echo TRANSLATION_LOGOUT;
	?>
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
       $("#message").html("<span style=\"color:green\"><?php
	
	echo TRANSLATION_CACHE_WAS_CLEARED;
	?></span>");

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
<strong><?php translate("website");?></strong><br/>
<form action="index.php?action=<?php echo htmlspecialchars($_GET["action"]);?>" id="site-form">
<select name="site-id" onchange="$('form#site-form').submit();">
<option value="default"><?php translate("default_website");?></option>
</select>
</form>
	<?php
    }
?>