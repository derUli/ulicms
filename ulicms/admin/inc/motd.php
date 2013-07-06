<?php if(defined("_SECURITY")){
if($_SESSION["group"]>=40){
?>
<div align="center">
<h2>Message Of The Day</h2>
<?php 
if(isset($_POST["motd"])){

  $motd = strip_tags($_POST["motd"], getconfig("allowed_html"));
  $motd = mysql_real_escape_string($motd);
  setconfig("motd", $motd);
  }

?>
<p>Die Message Of the Day wurde geändert.</p>
<?php }?>
<form action="index.php?action=motd" method="post">
<textarea name="motd" cols=60 rows=15><?php echo htmlspecialchars(getconfig("motd"));?></textarea>
<br>
<br>
<input type="submit" name="motd_submit" value="MOTD Ändern">
<p><strong>Erlaubte HTML-Tags:</strong><br/>
<?php echo htmlspecialchars(
getconfig("allowed_html"))?></p>
</form>
</div>


<?php

}else{
  noperms();
}

?>