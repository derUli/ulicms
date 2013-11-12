<?php
$acl = new ACL();
if(!is_admin() and !$acl -> hasPermission("categories")){
     noperms();
    
    }else{
    
    // Create
    if(isset($_REQUEST["create"])){
       if(!empty($_REQUEST["name"])){
          categories::addCategory($_REQUEST["name"]);
          }
    }

    // Delete
    if(isset($_GET["del"])){
       $del = intval($_GET["del"]);
       if($del != 1)
         categories::deleteCategory($del);
    }
    
    include_once ULICMS_ROOT.DIRECTORY_SEPERATOR."lib".DIRECTORY_SEPERATOR."string_functions.php";
    $categories = categories::getAllCategories();

?>

<?php if(!isset($_GET["add"]) and !isset($_GET["edit"])){ ?>

<p><a href="?action=categories&add">Kategorie Anlegen</a></p>
<?php }?>

<?php 
if(count($categories) > 0 and !isset($_GET["add"]) and !isset($_GET["edit"])){
?>
<table>
<tr>
<td><strong>ID</strong></td>
<td><strong>Name</strong></td>
<td></td>
<td></td>
</tr>
<?php foreach($categories as $category){?>
<tr>
<td><?php echo $category["id"];?></td>
<td><?php echo real_htmlspecialchars($category["name"]);?></td>
<td>[Bearbeiten]</td>
<td>[Löschen]</td>
</tr>
<?php }?>
</table>
<?php } else if(isset($_GET["add"])){?>
<h2>Kategorie anlegen</h2>
<form action="?action=categories" method="post">
<p>Name: <input type="text" name="name" value="">
<p><input type="submit" name="create" value="Anlegen"></p>
</form>

<?php }?>

<?php } ?>
