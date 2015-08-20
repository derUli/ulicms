<?php
if (defined ( "_SECURITY" )) {
	$acl = new ACL ();
	if ($acl->hasPermission ( "open_graph" )) {
	    if(isset($_POST["og_type"])){
               setconfig("og_type", db_escape($_POST["og_type"]));
            }
               
             if(isset($_POST["og_image"])){
               setconfig("og_image", db_escape($_POST["og_image"]));
              }
	    
	$og_type = getconfig("og_type");
	$og_image = getconfig("og_image");
	
	$og_url = "";
	
	if(!empty($og_image) and !startsWith($og_image, "http")){
           $og_url = get_protocol_and_domain(). $og_image;
   }
?>
<h1><?php translate("open_graph");?></h1>
<form action="index.php?action=open_graph" id="open_graph" method="post">
<?php csrf_token_html ();?>


<table border=0>
<tr>
<td>
<strong><?php translate("type");?></strong>
</td>
<td>
<input type="text" name="og_type" value="<?php echo htmlspecialchars($og_type);?>"/>
</td>
</tr>
<tr>
<td><strong><?php translate("image");?></strong></td>
<td>
<script type="text/javascript">
function openMenuImageSelectWindow(field) {
    window.KCFinder = {
        callBack: function(url) {
            field.value = url;
            window.KCFinder = null;
        }
    };
    window.open('kcfinder/browse.php?type=images&dir=images&lang=<?php echo htmlspecialchars(getSystemLanguage());?>', 'og_image',
        'status=0, toolbar=0, location=0, menubar=0, directories=0, ' +
        'resizable=1, scrollbars=0, width=800, height=600'
    );
}
</script>
<?php
if(!empty($og_url)){
?>
<div><img class="small-preview-image" src="<?php echo htmlspecialchars($og_url);?>"/></div>
<?php }?>

		<input type="text" id="og_image" name="og_image"
			readonly="readonly" onclick="openMenuImageSelectWindow(this)"
			value="<?php echo htmlspecialchars($og_image);?>" style="cursor: pointer" /><br /> <a href="#"
			onclick="$('#og_image').val('');return false;"><?php
		
		echo TRANSLATION_CLEAR;
		?>
		</a> 
		
		</td>
		</tr>
		<tr>
			<td></td>
			<td style="text-align: center"><input type="submit" name="submit"
				value="<?php
		
		translate("save_changes");
		?>"></td>
	</tr>
		</table>

</form>

<script type="text/javascript">
$("#open_graph").ajaxForm({beforeSubmit: function(e){
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