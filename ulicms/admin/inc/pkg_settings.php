<?php 
if(!is_admin()){
echo "<p class='ulicms_error'>Zugriff verweigert</p>";
} else {

// Wenn Formular abgesendet wurde, Wert Speichern
if(isset($_REQUEST["pkg_src"])){
   $new_pkg_src = trim($_REQUEST["pkg_src"]);
   if(!endsWith($new_pkg_src, "/"))
      $new_pkg_src .= "/";
      
   if($new_pkg_src == "/")
      deleteconfig("pkg_src");
   else
      setconfig("pkg_src", $new_pkg_src);
}

$default_pkg_src = "http://www.ulicms.de/packages/{version}/";

$version = new ulicms_version();
$version = $version->getInternalVersion();
$version = implode(".", $version);

$local_pkg_dir = "../packages/$version/";
$local_pkg_dir_value = "../packages/{version}/";
$pkg_src = getconfig("pkg_src");

$is_other = ($pkg_src !== $default_pkg_src and $pkg_src !== $local_pkg_dir and $pkg_src !== $local_pkg_dir_value);


include_once "../lib/file_get_contents_wrapper.php";
?>
<h1>Paketquelle</h1>
<form action="index.php?action=pkg_settings" method="post">
<input type="radio" name="radioButtonSRC"<?php if($pkg_src === $default_pkg_src) echo " checked";?> onclick="$('#sonstigePaketQuelle').slideUp(); $('#pkg_src').val('<?php echo $default_pkg_src?>');"> Offizielle Paketquelle [www.ulicms.de]<br>

<input type="radio" name="radioButtonSRC" <?php if($pkg_src === $local_pkg_dir or $pkg_src === $local_pkg_dir_value) echo " checked";?>  onclick="$('#sonstigePaketQuelle').slideUp(); $('#pkg_src').val('<?php echo $local_pkg_dir_value?>');"> Aus dem Dateisystem<br>


<input type="radio" name="radioButtonSRC" <?php if($is_other) echo " checked";?> onclick="$('#sonstigePaketQuelle').slideDown();"> Andere Paketquelle [URL]<br>
<div id="sonstigePaketQuelle" <?php 
if($is_other) 
  echo 'style="display:block"';
else
  echo 'style="display:none"';
  ?>>
<input style="width:400px" type="text" id="pkg_src" name="pkg_src" value="<?php echo htmlspecialchars($pkg_src);?>">
</div>

<br/>
<input type="submit" value="Einstellungen speichern"/>

</form>
<?php } ?>