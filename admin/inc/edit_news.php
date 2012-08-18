<?php if(defined("_SECURITY")){?>

<?php
  $news_id=intval($_GET["news"]);
  $query=mysql_query("SELECT * FROM ".tbname("news")." WHERE id=$news_id");
  $result=mysql_fetch_object($query);
?>
<?php 
if($_SESSION["group"]>=20){


?>
<form action="index.php?action=news" method="post">
<strong data-tooltip="Der Titel dieser News...">Titel der News:</strong><br/>
<input type="text" name="title" value="<?php echo htmlspecialchars($result->title)?>" style="width:300px;" >
<br/><br/>
<strong data-tooltip="Soll diese News öffentlich sichtbar sein?">aktiviert:</strong><br/>
<select name="activated" size=1>
<option value="1" <?php if($result->active==1){echo "selected";}?>>aktiviert</option>
<option value="0" <?php if($result->active==0){echo "selected";}?>>deaktiviert</option>
</select><br/><br/>
<strong data-tooltip="Beim ändern einer News wird das aktuelle Datum gesetzt.">Aktuelles Datum:</strong><br/>
<?php echo date(env("date_format"),time())?>
<br/><br/>
<textarea name="news_content" id="news_content" style="display:none;"><?php echo $result->content;?></textarea>
<textarea name="page_content" id="page_content" cols=60 rows=20><?php echo htmlspecialchars($row->content);?></textarea>
<script type="text/javascript">
var editor = CKEDITOR.replace( 'page_content',
					{
						skin : 'kama'
					});

</script>
<noscript>
<p style="color:red;">Der Editor benötigt JavaScript. Bitte aktivieren Sie JavaScript. <a href="http://jumk.de/javascript.html" target="_blank">[Anleitung]</a></p>
</noscript>
<br/><br/>
<input type="submit" value="Speichern">

</form>














<?php 
}
else{
noperms();
}
?>

<?php
}
?>

