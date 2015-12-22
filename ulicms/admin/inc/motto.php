<?php
if (defined ( "_SECURITY" )) {
	$acl = new ACL ();
	if ($acl->hasPermission ( "settings_simple" )) {
		
		$languages = getAllLanguages ();
		
		if (isset ( $_POST ["submit"] )) {
			for($i = 0; $i < count ( $languages ); $i ++) {
				
				$lang = $languages [$i];
				if (isset ( $_POST ["motto_" . $lang] )) {
					$page = db_escape ( $_POST ["motto_" . $lang] );
					setconfig ( "motto_" . $lang, $page );
					if ($lang == getconfig ( "default_language" )) {
						setconfig ( "motto", $page );
					}
				}
			}
		}
		
		$mottos = array ();
		
		for($i = 0; $i < count ( $languages ); $i ++) {
			$lang = $languages [$i];
			$mottos [$lang] = getconfig ( "motto_" . $lang );
			
			if (! $mottos [$lang])
				$mottos [$lang] = getconfig ( "motto" );
		}
		
		?>
<h1>
<?php
		
		echo TRANSLATION_MOTTO;
		?>
</h1>
<form action="index.php?action=motto" id="motto" method="post">
<?php
		
		csrf_token_html ();
		?>
	<table border=0>
		<tr>
			<td style="min-width: 100px;"><strong><?php
		
		echo TRANSLATION_LANGUAGE;
		?>
			</strong></td>
			<td><strong><?php
		
		echo TRANSLATION_MOTTO;
		?>
			</strong></td>
		</tr>
		<?php
		for($n = 0; $n < count ( $languages ); $n ++) {
			$lang = $languages [$n];
			?>
		<tr>
			<td><?php
			
			echo $lang;
			?></td>
			<td><input name="motto_<?php
			
			echo $lang;
			?>"
				style="width: 400px"
				value="<?php
			
			echo stringHelper::real_htmlspecialchars ( $mottos [$lang] );
			?>"></td>
			<?php
		}
		?>
		
		
		
		
		
		
		
		
		
		
		
		<tr>
			<td></td>
			<td style="text-align: center"><input type="submit" name="submit"
				value="Einstellungen Speichern"></td>
		</tr>

	</table>
</form>

<script type="text/javascript">
$("#motto_settings").ajaxForm({beforeSubmit: function(e){
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
}
?>