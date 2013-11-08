<?php 

// Todo neue ACL-Abfrage nutzen
if(!has_permissions("50")){
   echo "<p>Zugriff verweigert</p>";
} else {
include_once "../lib/string_functions.php";

$created = false;
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

}
?>
<h1>Gruppen</h1>
<?php if($created){?>
<p style='color:green;'>Die Gruppe "<?php echo $name;?>" wurde erfolgreich angelegt.</p>
<?php }?>
<?php if(!isset($_GET["add"]) and !isset($_GET["edit"]) and !isset($_GET["delete"])){

  include "inc/group_list.php";
} else if(isset($_GET["add"])){
  include "inc/group_add.php";
}?>

<?php }?>