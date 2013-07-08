<?php if(defined("_SECURITY")){
?>

<h2>Inhalte</h2>
<p><strong>Bitte wählen Sie einen Inhaltstyp aus:</strong></p>
<p><a href="index.php?action=pages">Seiten</a></p>
<p><a href="index.php?action=banner">Werbebanner</a></p>
<?php add_hook("content_type_list_entry");?>






<?php }?>
