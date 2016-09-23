<?php
if (! defined ( "ULICMS_ROOT" ))
	die ( "Schlechter Hacker!" );

$acl = new ACL ();
$url = null;
$table = null;

if (! $acl->hasPermission ( "export" )) {
	noperms ();
} else {
	$tables = db_get_tables ();
	?>
<h1><?php translate("json_export");?></h1>
<form action="?action=export" method="post">
<?php
	
	csrf_token_html ();
	?>
	<p>
	<?php translate("export_into_table");?>
		<br /> <select name="table" size="1">
		<?php
	
	foreach ( $tables as $name ) {
		?>
			<option value="<?php
		
		echo $name;
		?>"
				<?php
		
		if ($table == $name) {
			echo " selected=\"selected\"";
		}
		?>>
		<?php
		
		echo $name;
		?>
			</option>
			<?php
	}
	?>
		</select>
	</p>
	<input type="submit" name="submit"
		value="<?php translate("do_export");?>">
</form>

<?php
}

?>
