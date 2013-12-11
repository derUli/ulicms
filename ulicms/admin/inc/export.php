<?php
if(!defined("ULICMS_ROOT"))
   die("Schlechter Hacker!");

$acl = new ACL();
$url = null;
$table = null;

if(!$acl->hasPermission("export")){
   noperms();
} else {

  $tables = db_get_tables();
  
  if(isset($_POST["table"])){
    $table = db_escape($_POST["table"]);
    $json = ExportHelper::table2JSON($table);
    $filename = basename($table)."-".time().".json";
    $url = "../content/tmp/".$filename;
    $handle = fopen(ULICMS_TMP.$filename, "w");
    fwrite($handle, $json);
    fclose($handle);
  }

  
?>
  <h1>JSON Export</h1>
  <?php if(!is_null($url) and !is_null($table)){
     echo "<p><a href=\"".$url."\" target=\"blank\">Export der Tabelle ".$table." im JSON-Format runterladen</a></p>";
  }
  ?>
  <form action="?action=export" method="post">
  <p>Exportiere Tabelle:<br/>
  <select name="table" size="1">
  <?php foreach($tables as $name){?>
  <option value="<?php echo $name;?>" <?php if($table == $name){ echo " selected=\"selected\"";}?>><?php echo $name;?></option>
  <?php }?>
  </select>
  </p>
  <input type="submit" name="submit" value="Exportieren">
  </form>
  
  <?php
  }

  ?>
