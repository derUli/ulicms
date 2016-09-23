<?php
$acl = new ACL ();
if ($acl->hasPermission ( "motd" )) {
	?>
<script type="text/javascript">
function filter_by_language(element){
   var index = element.selectedIndex
   if(element.options[index].value != ""){
     location.replace("index.php?action=pages&filter_language=" + element.options[index].value)
   }
}

</script>
<div>
	<h2>
	<?php
	
	translate ( "motd" );
	?>
	</h2>
	<?php
	if (isset ( $_POST ["motd"] )) {
		
		$motd = strip_tags ( $_POST ["motd"], Settings::get ( "allowed_html" ) );
		$motd = db_escape ( $motd );
		setconfig ( "motd", $motd );
		
		?>
	<p>
	<?php translate("motd_was_changed");?>
	</p>
	<?php
	}
	?>

	<form id="motd_form" action="index.php?action=motd" method="post">
	<?php
	
	csrf_token_html ();
	?>
		<textarea name="motd" cols=60 rows=15><?php
	
	echo htmlspecialchars ( Settings::get ( "motd" ) );
	?></textarea> <br> <br> <input type="submit" name="motd_submit"
			value="<?php translate("save_changes");?>">
		<p>
			<strong><?php translate("allowed_html_tags");?>
			</strong><br />
			<?php
	
	echo htmlspecialchars ( Settings::get ( "allowed_html" ) )?>
		</p>
		<?php
	if (Settings::get ( "override_shortcuts" ) == "on" || Settings::get ( "override_shortcuts" ) == "backend") {
		?>
		<script type="text/javascript" src="scripts/ctrl-s-submit.js">
</script>
<?php
	}
	?>
	</form>
</div>
<script type="text/javascript">
$("#motd_form").ajaxForm({beforeSubmit: function(e){
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
} else {
	noperms ();
}

?>
