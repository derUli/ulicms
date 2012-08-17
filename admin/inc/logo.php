<?php if(defined("_SECURITY")){

if($_SESSION["group"]>=40){
?>     
<?php if($_GET["error"] == "to_big"){ ?> 
<p style="color:red; font-size:1.2em">Die von Ihnen hochgeladene Grafik ist zu groß.</p>
<?php
}?>
<p>Laden Sie ein beliebiges Logo hoch, welches im Head Bereich Ihrer Homepage angezeigt wird.<br>
Sie können das Logo in den Grundeinstellungen deaktivieren.<br>
Das Bild darf maximal 500 x 100 Pixel haben.
</p>
<form enctype="multipart/form-data" action="index.php?action=logo_upload" method="post">
<table border=0 height=250>
<tr>
<td><strong>Ihr Logo</strong></td>
<td><?php

$logo_path = "../content/images/".getconfig("logo_image");
if (file_exists($logo_path) and is_file($logo_path)){
  echo '<img class="website_logo" src="'.$logo_path.'" alt="'.getconfig("homepage_title").'"/>';

}?>
</td>
<tr>             
<td width=480><strong>Neues Logo hochladen</strong></td>
<td><input name="logo_upload_file" type="file">
<br/>
</td>
</table>
<div align="center">
<input type="submit" value="Hochladen">
</div>

</form>

<?php 
}else{
  noperms();
}



}

?>