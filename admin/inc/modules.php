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
    echo "<li><strong>";
	
	
	$module_has_admin_page = file_exists(getModuleAdminFilePath($modules[$i]));
	

	echo $modules[$i];
	echo "</strong>";
	
	
	if($module_has_admin_page){
	   echo " <a style=\"font-size:0.8em;\" href=\"?action=module_settings&module=".$modules[$i]."\">";
	   echo " [Einstellungen]";
	   echo "</a>";
	}
	
	echo "<br/><input type='text' value='[module=\"".$modules[$i]."\"]'><br/><br/></li>";
  }
  echo "</ol>";
  
  }
?>

<hr/>
