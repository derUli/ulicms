<?php
// @FIXME: Alle SQLs die in dieser Datei stehen, müssen in einen Controller oder ein Model ("HomeViewModel" anlegen) ausgelagert werden.
$acl = new ACL ();
include_once ULICMS_ROOT . "/lib/formatter.php";

$controller = ControllerRegistry::get ();
$model = $controller->getModel ();

if ($acl->hasPermission ( "dashboard" )) {
	
	?>
<p>
<?php
	$str = get_translation ( "hello_name" );
	$str = str_ireplace ( "%firstname%", $_SESSION ["firstname"], $str );
	$str = str_ireplace ( "%lastname%", $_SESSION ["lastname"], $str );
	echo $str;
	?>
	[<a href="?action=admin_edit&admin=<?php echo $_SESSION["login_id"]?>"><?php translate("edit_profile");?></a>]
</p>
<?php
	$motd = get_lang_config ( "motd", getSystemLanguage () );
	if ($motd or strlen ( $motd ) > 10) {
		?>

<div id="accordion-container">

	<h2 class="accordion-header">
	<?php translate("motd");?></h2>
	<div class="accordion-content">
	<?php
		echo $motd;
		?>
	</div>
		<?php }?>
	<div id="patch-notification" style="display: none;">
		<h2 class="accordion-header">
	<?php translate ( "there_are_patches_available" );	?>
	</h2>
		<div class="accordion-content" id="patch-message"></div>
	</div>
<?php
	$pi = ULICMS_ROOT . "/post-install.php";
	if (file_exists ( $pi ) and is_writable ( $pi )) {
		?>
<h2 class="accordion-header"><?php translate("unfinished_package_installations");?></h2>

	<div class="accordion-content">
		<a href="index.php?action=do-post-install">
			<?php translate("there_are_unfinished_package_installations");?></a>
	</div>
	<?php } ?>
				<div id="core-update-check" style="display: none">
		<h2 class="accordion-header">
	<?php translate("update_available");?>
	</h2>
		<div class="accordion-content" id="core-update-message"></div>
	</div>
	<?php
	
	if (! Settings::get ( "disable_ulicms_newsfeed" )) {
		?>
	<h2 class="accordion-header">
	<?php translate("ulicms_news");?></h2>
	<div class="accordion-content" id="ulicms-feed">
		<img src="gfx/loading.gif" alt="Feed wird geladen..." />
	</div>
	<script type="text/javascript">
$(document).ready(function() {
 $('#ulicms-feed').load('?action=ulicms_news');
});
</script>
<?php } ?>
	<h2 class="accordion-header">
	<?php translate("statistics");?>
	</h2>
	<div class="accordion-content">
		<table>
		<?php
	$installed_at = Settings::get ( "installed_at" );
	if ($installed_at) {
		$time = time () - $installed_at;
		$formatted = formatTime ( $time );
		?>
			<tr>
				<td><?php translate("site_online_since");?></td>
				<td><?php
		
		echo $formatted;
		?></td>
			</tr>
			<?php
	}
	?>
			<tr>
				<td><?php translate("pages_count");?>
				</td>
				<td><?php echo $model->contentCount;?></td>
			</tr>
			<tr>
				<td><?php translate("REGISTERED_USERS_COUNT");?>
				</td>
				<td><?php echo count(getUsers())?></td>
			</tr>
			<?php
	
	if (Settings::get ( "contact_form_refused_spam_mails" ) !== false) {
		?>
			<tr>
				<td><?php echo translate("BLOCKED_SPAM_MAILS");?></td>
				<td><?php echo Settings::get("contact_form_refused_spam_mails")?></td>
			</tr>
			<?php
	}
	?>
			<?php
	// @FIXME: Das hier gehört ins Guestbook Modul
	$test = db_query ( "SELECT id FROM " . tbname ( "guestbook_entries" ) );
	if ($test) {
		?>
			<tr>
				<td><?php translate("GUESTBOOK_ENTRIES");?></td>
				<td><?php echo db_num_rows($test)?></td>
			</tr>
			<?php
	}
	?>
		</table>
	</div>
	<h2 class="accordion-header">
	<?php translate("online_now");?>
	</h2>
	<div class="accordion-content">
		<ul id="users_online">
<?php
	
	foreach ( getOnlineUsers () as $user ) {
		?>
<li><?php Template::escape($user);?></li>
<?php } ?>
		</ul>
	</div>
	<h2 class="accordion-header">
	<?php translate("top_pages");?>
	</h2>
	<div class="accordion-content">
		<table cellpadding="2">
			<tr style="font-weight: bold;">
				<td><?php translate("title");?>
				</td>
				<td><?php translate("views");?>
				</td>
			</tr>
			<?php
	foreach ( $model->topPages as $row ) {
		
		$domain = getDomainByLanguage ( $row->language );
		if (! $domain) {
			$url = "../" . $row->systemname . ".html";
		} else {
			$url = "http://" . $domain . "/" . $row->systemname . ".html";
		}
		?>
			<tr>
				<td><a href="<?php
		
		echo $url;
		?>" target="_blank"><?php
		
		echo htmlspecialchars ( $row->title, ENT_QUOTES, "UTF-8" );
		?></a></td>
				<td align="right"><?php
		
		echo $row->views;
		?></td>
				<?php
	}
	?>
			</tr>
		</table>
	</div>

	<h2 class="accordion-header"><?php translate("last_changes");?>
	</h2>
	<div class="accordion-content">
		<table cellpadding="2">
			<tr style="font-weight: bold;">
				<td><?php translate("title");?>
				</td>
				<td><?php translate("date");?>
				</td>
				<td><?php translate("done_by");?>
				</td>
			</tr>
			<?php
	foreach ( $model->lastModfiedPages as $row ) {
		$domain = getDomainByLanguage ( $row->language );
		if (! $domain) {
			$url = "../" . $row->systemname . ".html";
		} else {
			$url = "http://" . $domain . "/" . $row->systemname . ".html";
		}
		
		?>
			<tr>
				<td><a href="<?php
		echo $url;
		?>" target="_blank"><?php
		
		echo htmlspecialchars ( $row->title, ENT_QUOTES, "UTF-8" );
		?></a></td>

				<td><?php echo strftime("%x %X", $row -> lastmodified)?></td>
				<td><?php
		$autorName = $model->admins [$row->lastchangeby];
		if (! empty ( $autorName )) {
		} else {
			$autorName = $model->admins [$row->autor];
		}
		echo $autorName;
		?></td>
			</tr>
			<?php
	}
	?>
		</table>
	</div>
	<?php
	add_hook ( "accordion_layout" );
	?>
</div>
<script src="scripts/dashboard.js" type="text/javascript"></script>
<?php
} else {
	noperms ();
}
