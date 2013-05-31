<?php 
if(is_admin()){



// Modul deinstallieren
if(isset($_GET["remove"])){
   $remove = basename($_GET["remove"]);
   $uninstalled = uninstall_module($remove );
   if($uninstalled)
      echo "<p style=\"color:green;\">".htmlspecialchars($remove).
      " wurde erfolgreich deinstalliert.</p>";
   else
      echo "<p style=\"color:red;\">".htmlspecialchars($remove ).
      " konnte nicht deinstalliert werden.<br/>Bitte löschen Sie das Modul manuell vom Server</p>";
   
   
}
?>
<p><a href="?action=available_modules">[Module installieren]</a></p> 
<?php } ?>
<strong>Installierte Module:</strong>
<p>Hier finden Sie eine Auflistung der installierten Module.<br/>
<br/>
Darunter befindet sich der Code, den Sie in eine Seite einfügen müssen,
um diesen Modul einzubetten</p>

<?php 
$modules = getAllModules();
if(count($modules)>0){
  echo "<ol>";
  for($i=0; $i<count($modules); $i++){
    echo "<li style=\"margin-bottom:10px;border-bottom:solid #cdcdcd 1px;\"><strong>";
	
	$module_has_admin_page = file_exists(getModuleAdminFilePath($modules[$i]));
	

	echo $modules[$i];
	echo "</strong>";
	
        echo "<div style=\"float:right\">";
	
	if($module_has_admin_page){
	   echo " <a style=\"font-size:0.8em;\" href=\"?action=module_settings&module=".$modules[$i]."\">";
	   echo " [Einstellungen]";
	   echo "</a>";
	}
	
	echo " <a style=\"font-size:0.8em;\" href=\"?action=modules&remove=".$modules[$i]."\" onclick=\"return confirm('Möchten Sie das Modul ".$modules[$i]." wirklich deinstallieren?')\">";
	   echo " [Entfernen]";
	   echo "</a>";
	   
	   
        echo "</div>";
	
	echo "<br/><input type='text' value='[module=\"".$modules[$i]."\"]'><br/><br/></li>";
  }
  echo "</ol>";
  
  }
?>

<hr/>
