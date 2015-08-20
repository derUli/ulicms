<?php
if (defined ( "_SECURITY" )) {
	$acl = new ACL ();
	if ($acl->hasPermission ( "open_graph" )) {
	
	$og_type = getconfig("og_type");
	$og_image = getconfig("og_image");
	
	$og_url = "";
	
	if(!empty($og_image) and !startsWith($og_image, "http")){
           $og_url = get_protocol_and_domain(). $og_image;
   }
?>
<form action="index.php?action=open_graph" id="open_graph" method="open_graph">
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
<div><img class="responsive-image" src="<?php echo htmlspecialchars($og_url);?>"/></div>
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
		</table>

</form>


<?php
	} else {
		noperms ();
	}
    
    }
?>