<?php {
if(defined("_SECURITY")){
$pages_count=mysql_num_rows(mysql_query("SELECT * FROM ".tbname("content")));
$news_count=mysql_num_rows(mysql_query("SELECT * FROM ".tbname("news")));
$topPages=mysql_query("SELECT * FROM ".tbname("content")." WHERE notinfeed = 0 AND systemname <> \"kontakt\" ORDER BY views DESC LIMIT 5");
$lastModfiedPages = mysql_query("SELECT * FROM ".tbname("content")." WHERE systemname <> \"kontakt\" ORDER BY lastmodified DESC LIMIT 5");

$admins_query = mysql_query("SELECT * FROM ".tbname("admins"));

$admins = Array();

while($row = mysql_fetch_object($admins_query)){
	$admins[$row->id] = $row->username;
}

?>
<p>Hallo <?php echo $_SESSION["firstname"]." ".$_SESSION["lastname"];?>!</p>

<p>Diese Website hat <?php echo $pages_count?> Seiten und <?php echo $news_count?> News.</p>
<p>Die meistgelesenen Artikel sind:
<table cellpadding="2" border=0>
<tr style="font-weight:bold;">
<td>Titel</td>
<td>Views</td>
</tr>
<?php while($row = mysql_fetch_object($topPages) ){?>
<tr>
<td><a href="../?seite=<?php echo $row->systemname;?>" target="_blank"><?php echo $row->title;?></a></td>
<td align="right"><?php echo $row->views;?></td>
<?php }?>
</tr>
</table>
</p>
<br>
<p><strong>Letzte Änderungen:</strong></p>
<table cellpadding="2" style="width: 70%; border:0px;">
<tr style="font-weight:bold;">
<td>Titel</td>
<td>Datum</td>
<td>Durchgeführt von</td>
</tr>

<?php while($row = mysql_fetch_object($lastModfiedPages) ){?>
<tr>
<td><a href="../?seite=<?php echo $row->systemname;?>" target="_blank"><?php echo $row->title;?></a></td>

<td><?php echo date(env("date_format"), $row->lastmodified)?></td>
<td>
<?php 
$autorName = $admins[$row->lastchangeby];
if(!empty($autorName)){
}else{
$autorName = $admins[$row->autor];
}

echo $autorName;
?></td>
<?php }?>
</tr>
</table>

</p>

<br>
<br><br>

<?php
}

}
?>