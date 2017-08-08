<form action="index.php" method="get">
	<input type="hidden" name="action" value="module_settings"> <input
		type="hidden" name="module" value="fortune2"><input type="submit"
		value="RESET">
</form>
<br />

<form action="index.php" method="get">
	<input type="hidden" name="action" value="module_settings"> <input
		type="hidden" name="module" value="fortune2"> <input type="hidden"
		name="sClass" value="Fortune"> <input type="hidden" name="sMethod"
		value="doSomething"> <input type="submit" value="GET">
</form>
<br />
<form
	action="<?php Template::escape(ModuleHelper::buildAdminURL("fortune2", "sClass=Fortune&sMethod=doSomething"));?>"
	method="post">
	<?php csrf_token_html();?>
	<input type="submit" value="POST">
</form>
<br />
<code><?php if(ViewBag::get("sample_text")){?>
<?php Template::escape(ViewBag::get("sample_text"));?>
<?php }?></code>