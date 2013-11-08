<?php 
// Todo neue ACL-Abfrage nutzen
if(!has_permissions("50")){
   echo "<p>Zugriff verweigert</p>";
} else {
?>
<h1>Gruppen</h1>
<?php if(!isset($_GET["add"]) and !isset($_GET["edit"]) and !isset($_GET["delete"])){

include "inc/group_list.php";
} else if(isset($_GET["add"])){
include "inc/group_add.php";
}?>

<?php }?>