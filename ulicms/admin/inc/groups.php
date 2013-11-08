<?php 

// Todo neue ACL-Abfrage nutzen
if(!has_permissions("50")){
   echo "<p>Zugriff verweigert</p>";
} else {
include_once "../lib/string_functions.php";

$modified = false;
$created = false;
$removed = false;

if(isset($_POST["add_group"])){
   $acl = new ACL();
   $all_permissions = $acl->getDefaultACL(false, true);
   
   foreach($_POST["user_permissons"] as $permission_name){
      $all_permissions[$permission_name] = true;
   }
   
   $name = trim($_POST["name"]);
   if(!empty($name)){
     $acl->createGroup($name, $all_permissions);
     $created = true;
     $name = real_htmlspecialchars($name);
}

} else if(isset($_GET["delete"])){
$id = intval($_GET["delete"]);
$acl = new ACL();
$acl->deleteGroup($id);
$removed = true;

}else if(isset($_POST["edit_group"])){
   $acl = new ACL();
   $all_permissions = $acl->getDefaultACL(false, true);
   $id = $_POST["id"];
   
   foreach($_POST["user_permissons"] as $permission_name){
      $all_permissions[$permission_name] = true;
   } 
   
   $name = trim($_POST["name"]);
   $all_permissions = json_encode($all_permissions);
   if(!empty($name)){
     $acl->updateGroup($id, $name, $all_permissions);
     $modified = true;
     $name = real_htmlspecialchars($name);
}
}
?>
<h1>Gruppen</h1>
<?php if($created){?>
<p style='color:green;'>Die Gruppe "<?php echo $name;?>" wurde erfolgreich angelegt.</p>
<?php }?>

<?php if($modified){?>
<p style='color:green;'>Die Gruppe "<?php echo $name;?>" wurde erfolgreich bearbeitet.</p>
<?php }?>

<?php if($removed){?>
<p style='color:green;'>Die Gruppe wurde erfolgreich gelöscht</p>
<?php }?>

<?php if(!isset($_GET["add"]) and !isset($_GET["edit"])){

  include "inc/group_list.php";
} else if(isset($_GET["add"])){
  include "inc/group_add.php";
}else if(isset($_GET["edit"])){
  include "inc/group_edit.php";
}
?>

<?php }?>