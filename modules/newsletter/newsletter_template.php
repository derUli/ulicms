<?php 
define("NEWSLETTER_TEMPLATE_TITLE", getconfig("newsletter_template_title"));
define("NEWSLETTER_TEMPLATE_CONTENT", getconfig("newsletter_template_content"));

?>

<h3>Vorlage</h3>
<form method="post" action="<?php echo getModuleAdminSelfPath()?>">
<p><strong>Titel: </strong><input type="text" maxlength=78 size=78 name="template_title" value="<?php echo htmlspecialchars(NEWSLETTER_TEMPLATE_TITLE)?>"></p>


<p>
<textarea id="template_content"name="template_content" cols=60 rows=16><?php 
echo htmlspecialchars(NEWSLETTER_TEMPLATE_CONTENT);
?></textarea></p>
<script type="text/javascript">
var editor = CKEDITOR.replace( 'template_content',
					{
						skin : 'kama'
					});                                         

</script>

<p>
<input type="submit" name="submit" value="Vorlage speichern">
</p>
</form>