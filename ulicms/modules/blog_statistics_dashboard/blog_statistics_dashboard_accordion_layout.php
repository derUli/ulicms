<?php 
if(in_array("blog", getAllModules())){
   ?>

<?php 
$query = db_query("SELECT * FROM ".tbname("blog"));
$blog_post_count = mysql_num_rows($query);


$query = db_query("SELECT * FROM ".tbname("blog_comments"));
$comment_count = mysql_num_rows($query);


$query = mysql_query("SELECT * FROM ".tbname("blog"). " ORDER by `views` DESC LIMIT 5");


?>
</div>

<h2 class="accordion-header">Blog Statistiken</h2>
<div class="accordion-content">
<table style="400px;">
<tr>
<td><strong>Anzahl der Blogposts:</td>
<td style="text-align:right;"><?php echo intval($blog_post_count);?>
</tr>
<tr>
<td><strong>Anzahl der Kommentare:</td>
<td style="text-align:right;"><?php echo intval($comment_count);?>
</tr>
</table>
</div>
<h2 class="accordion-header">Beliebteste Blogartikel</h2>
<div class="accordion-content">
<?php if($blog_post_count === 0){
echo "<p>Es sind noch keine Blogartikel vorhanden.</p>";
} else ?>
<table>
<tr>
<td><strong>Titel</strong>
<td>Views</strong>
</tr>
<?php 
while($row = mysql_fetch_object($query)){
?>
<tr>
<td><?php echo htmlspecialchars($row->title, ENT_QUOTES, "UTF-8");?></td>
<td style="text-align:right; padding-left:10px;"><?php echo $row->views;?></td>
<?php
}
?>
</table>

</div>


<?php
}
?>
