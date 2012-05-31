<?php if(defined("_SECURITY")){

if($_SESSION["group"]>=20){


?>
<form action="index.php?action=news" method="post">
<strong data-tooltip="Der Titel dieser News...">Titel der News:</strong><br/>
<input type="text" name="title" value="" style="width:300px;" >
<br/><br/>
<strong data-tooltip="Soll diese News öffentlich sichtbar sein?">aktiviert:</strong><br/>
<select name="activated" size=1>
<option value="1" selected>aktiviert</option>
<option value="0" >deaktiviert</option>
</select><br/><br/>
<strong data-tooltip="Das Veröffentlichungsdatum der neu angelegten News">Aktuelles Datum:</strong><br/>
<?php echo date(env("date_format"),time())?>
<br/><br/>
<textarea name="news_content" id="news_content" style="display:none;"></textarea>
<script type="text/javascript">
document.getElementById("news_content").style.display="block";
var editor = CKEDITOR.replace( 'news_content',
					{
						skin : 'kama'
					});
					


</script>
<noscript>
<p style="color:red;">Der Editor benötigt JavaScript. Bitte aktivieren Sie JavaScript. <a href="http://jumk.de/javascript.html" target="_blank">[Anleitung]</a></p>
</noscript>
<input type="hidden" name="add_news" value="add_news">
</form>











<?php 
}else{
noperms();
}

?>

<?php }?>

