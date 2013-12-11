<?php
if(!defined("ULICMS_ROOT"))
   die("Schlechter Hacker!");

$acl = new ACL();
$table = null;

if(!$acl->hasPermission("import")){
   noperms();
} else {
  $valid = null;
  $do_update = false;

  $tables = db_get_tables();
  
  if(isset($_POST["table"]) and isset($_FILES['file'])){
    $table = db_escape($_POST["table"]);
    $json = ExportHelper::table2JSON($table);
    $do_update = isset($_POST["do_update"]);
    $filename = $_FILES['file']['tmp_name'];
    $data = file_get_contents($filename);
    $valid = !is_null(@json_decode($data));
    if($valid){
    $importer = new ImportHelper();
     $importer->importJSON($table, $data, $do_update);
    }
    
  }

  
?>
  <h1>JSON Import</h1>
  <?php if($valid === false){
  ?>
  <p class="ulicms_error">Diese Datei ist nicht im JSON Format</p>
   <?php }?>
  <form action="?action=import" method="post" enctype="multipart/form-data">
  <p>Importiere in Tabelle:<br/>
  <select name="table" size="1">
  <?php foreach($tables as $name){?>
  <option value="<?php echo $name;?>" <?php if($table == $name){ echo " selected=\"selected\"";}?>><?php echo $name;?></option>
  <?php }?>
  </select>
  </p>
  <p><input type="file" name="file"></p>
  <p><input type="checkbox" name="do_update" <?php if($do_update){ echo "checked"; } ?>><label for="do_update">Update durchführen</label></p>
  <input type="submit" name="submit" value="Export">
  </form>
  
  <?php
  }

  ?>
