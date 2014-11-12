<?php
if(!defined("ULICMS_ROOT"))
     die("Schlechter Hacker!");

 include_once ULICMS_ROOT . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "string_functions.php";

$acl = new ACL();
$table = null;
$errors = array();

if(!$acl -> hasPermission("import")){
     noperms();
    }else{
     $valid = null;
     $do_update = false;
    
     $tables = db_get_tables();
    
     if(isset($_POST["table"]) and is_uploaded_file($_FILES['file']['tmp_name'])){
         $table = db_escape($_POST["table"]);
         $json = ExportHelper :: table2JSON($table);
         $do_update = isset($_POST["do_update"]);
         $filename = $_FILES['file']['tmp_name'];
         $data = file_get_contents($filename);
         $valid = !is_null(@json_decode($data));
         if($valid){
             $importer = new ImportHelper();
             $importer -> importJSON($table, $data, $do_update);
             $errors = $importer -> getErrors();
             }
        
         }
    
    
    ?>
  <h1><?php echo TRANSLATION_JSON_IMPORT;?></h1>
  <?php if(count($errors) > 0){
         foreach($errors as $e){
             ?>
   <p class="ulicms_error"><?php real_htmlspecialchars($e[0]);
            ?></p>
   <?php
             }
         }else if($valid === true){
        ?>
 
  <p class="green">Import in Tabelle <?php real_htmlspecialchars($table);
        ?> erfolgreich!</p>
  <?php }
    ?>
  <?php if($valid === false){
         ?>
  <p class="ulicms_error">Diese Datei ist nicht im JSON Format!</p>
   <?php }
    ?>
  <form action="?action=import" method="post" enctype="multipart/form-data">
  <p><?php echo TRANSLATION_IMPORT_INTO_TABLE;?><br/>
  <select name="table" size="1">
  <?php foreach($tables as $name){
        ?>
  <option value="<?php echo $name;
        ?>" <?php if($table == $name){
            echo " selected=\"selected\"";
        }
        ?>><?php echo $name;
        ?></option>
  <?php }
    ?>
  </select>
  </p>
  <p><input type="file" name="file"></p>
  <p><input id="do_update" type="checkbox" name="do_update" <?php if($do_update){
        echo "checked";
    }
    ?>><label for="do_update"><?php echo TRANSLATION_DO_IMPORT;?></label></p>
  <input type="submit" name="submit" value="Importieren">
  </form>
  
  <?php
     }

 ?>
