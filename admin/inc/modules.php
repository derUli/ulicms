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
    echo "<li>".$modules[$i]."<br/><input type='text' value='[module=\"".$modules[$i]."\"]'></li>";
  }
  echo "</ol>";
  
  }
?>

<hr/>
