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
	
	echo TRANSLATION_MOTD;
	?>
	</h2>
	<?php
	if (isset ( $_POST ["motd"] )) {
		
		$motd = strip_tags ( $_POST ["motd"], getconfig ( "allowed_html" ) );
		$motd = db_escape ( $motd );
		setconfig ( "motd", $motd );
		
		?>
	<p>
	<?php
		
		echo TRANSLATION_MOTD_WAS_CHANGED;
		?>
	</p>
	<?php
	}
	?>

	<form id="motd_form" action="index.php?action=motd" method="post">
	<?php
	
	csrf_token_html ();
	?>
		<textarea name="motd" cols=60 rows=15><?php
	
	echo htmlspecialchars ( getconfig ( "motd" ) );
	?></textarea> <br> <br> <input type="submit" name="motd_submit"
			value="<?php
	
	echo TRANSLATION_SAVE_CHANGES;
	?>">
		<p>
			<strong><?php
	
	echo TRANSLATION_ALLOWED_HTML_TAGS;
	?>
			</strong><br />
			<?php
	
	echo htmlspecialchars ( getconfig ( "allowed_html" ) )?>
		</p>
		<?php
	if (getconfig ( "override_shortcuts" ) == "on" || getconfig ( "override_shortcuts" ) == "backend") {
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
