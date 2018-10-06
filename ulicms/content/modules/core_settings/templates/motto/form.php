<?php
$acl = new ACL();
if ($acl->hasPermission("settings_simple")) {
    $languages = getAllLanguages();
    $mottos = array();
    for ($i = 0; $i < count($languages); $i ++) {
        $lang = $languages[$i];
        $mottos[$lang] = Settings::get("motto_" . $lang);
        
        if (! $mottos[$lang]) {
            $mottos[$lang] = Settings::get("motto");
        }
    }
    ?><p>
	<a href="<?php echo ModuleHelper::buildActionURL("settings_simple");?>"
		class="btn btn-default btn-back"><?php translate("back")?></a>
</p>
<h1>
<?php translate("motto");?>
</h1>
<?php
    echo ModuleHelper::buildMethodCallForm("MottoController", "save", array(), "post", array(
        "id" => "motto_settings"
    ));
    ?>
<table border=0>
	<tr>
		<td style="min-width: 100px;"><strong><?php
    
    translate("language");
    ?>
</strong></td>
		<td><strong><?php translate("motto");?>
			</strong></td>
	</tr>
<?php
    for ($n = 0; $n < count($languages); $n ++) {
        $lang = $languages[$n];
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
        
        echo StringHelper::real_htmlspecialchars($mottos[$lang]);
        ?>"></td>
			<?php
    }
    ?>
</tr>
	<tr>
		<td></td>
		<td style="text-align: center">

			<button type="submit" name="submit" class="btn btn-primary">Einstellungen
				Speichern</button>
		</td>
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
    noPerms();
}
