<?php
if (defined ( "_SECURITY" )) {
	$acl = new ACL ();
	if (is_admin () or $acl->hasPermission ( "update_system" )) {
		?>

		<?php
		if (faster_file_exists ( "../update.php" )) {
			?>
<p>
	<a href="../update.php"><?php translate("run_update");?></a>
</p>
<?php translate("update_notice");?>
	<?php
		} else {
			?>
			<?php translate("update_information_text");	?>
<p>

<?php
		}
		?>

		<?php
	} else {
		noperms ();
	}
}
?>