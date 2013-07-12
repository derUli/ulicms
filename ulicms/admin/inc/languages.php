<h2>Sprachen verwalten</h2>
<?php if(defined("_SECURITY")){
     if($_SESSION["group"] >= 50){
        
         $languages = db_query("SELECT * FROM " . tbname("languages") . " ORDER BY language_code ASC");
        
         ?>
<form action="index.php?action=languages" method="post">
<table border=0>
<tr>
<td><strong>Kürzel:</strong> </td>
<td><input type="text" name="language_code" maxlength=6 size=6></td>
</tr>
<tr>
<td style="width:100px;"><strong>Volle Bezeichnung:</strong> </td>
<td><input type="text" name="name" maxlength=100 size=40></td>
</tr>
<tr>
<td></td>
<td><input type="submit" name="add_language" value="Sprache hinzufügen"></td>
</tr>  

</table>
</form>
<br>
<hr>
<br>
<?php if(mysql_num_rows($languages) > 0){
             ?>
<table border=1 style="width:800px;"> 
<tr>
<td><strong>Kürzel</strong></td>
<td><strong>Volle Bezeichnung</strong></td>
<td align="center"><strong>Standard</strong></td>
<td></td>
</tr>
<?php
             while($row = mysql_fetch_object($languages)){
                 ?>
<tr>
<td>
<?php echo htmlspecialchars($row -> language_code)?>
</td>
<td><?php echo htmlspecialchars($row -> name);
                 ?></td>

<td align="center" style="font-weight:bold;">
<?php
                 if($row -> language_code === getconfig("default_language")){
                     echo "<span style='color:green !important;'>Ja</span>";
                     }else{
                     ?>
<a onclick="return confirm('Möchten Sie <?php echo $row -> name;
                     ?> als Standard festlegen?')" href="index.php?action=languages&default=<?php echo $row -> language_code?>">
<span style="color:red !important;">Nein</span></a>
<?php
                     }
                 ?>
</td>

<td align="center">
<?php if($row -> language_code == getconfig("default_language")){
                     ?>
<a onclick="javascript:alert('Die Standardsprache kann nicht gelöscht werden!')" href="#">
<img src="gfx/delete.gif" alt="Löschen" title="Löschen"></a>
</a>
<?php
                     }else{
                     ?>
<a onclick="return confirm('Möchten Sie die Sprache <?php echo $row -> name;
                     ?> wirklich löschen?')" href="index.php?action=languages&delete=<?php echo $row -> id?>">
<img src="gfx/delete.gif" alt="Löschen" title="Löschen"></a>
</a>
<?php }
                 ?>
</td>



<?php }
             ?>
</table>
<?php
             }
         ?>



<?php
         }
    else{
         noperms();
         }
     ?>

<?php }
?>
