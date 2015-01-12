<?php
$acl = new ACL();
if(!is_admin() and !$acl -> hasPermission("categories")){
     noperms();
    
     }else{
    
     // Create
    if(isset($_REQUEST["create"])){
         if(!empty($_REQUEST["name"])){
             categories :: addCategory($_REQUEST["name"]);
             }
         }
    
     // Create
    if(isset($_REQUEST["update"])){
         if(!empty($_REQUEST["name"]) and !empty($_REQUEST["id"])){
             categories :: updateCategory(intval($_REQUEST["id"]), $_REQUEST["name"]);
             }
         }
    
     // Delete
    if(isset($_GET["del"])){
         $del = intval($_GET["del"]);
         if($del != 1)
             categories :: deleteCategory($del);
         }
    
     include_once ULICMS_ROOT . DIRECTORY_SEPERATOR . "lib" . DIRECTORY_SEPERATOR . "string_functions.php";
     if(isset($_GET["order"]) and in_array($_GET["order"], array("id", "name")))
         $order = basename($_GET["order"]);
     else
         $order = "id";
    
     $categories = categories :: getAllCategories($order);
    
     ?>

<?php
     if(!isset($_GET["add"]) and !isset($_GET["edit"])){
         ?>

<h2><?php echo TRANSLATION_CATEGORIES;
        ?></h2>
<p><?php echo TRANSLATION_CATEGORIES_INFOTEXT;
        ?></p>

<p><a href="?action=categories&add"><?php echo TRANSLATION_CREATE_CATEGORY;
        ?></a></p>
<?php }
     ?>

<?php
     if(count($categories) > 0 and !isset($_GET["add"]) and !isset($_GET["edit"])){
         ?>
<table>
<tr>
<td style="min-width:100px;"><a href="?action=categories&order=id"><strong><?php echo TRANSLATION_ID;
        ?></strong></a></td>
<td style="min-width:200px;"><a href="?action=categories&order=name"><strong><?php echo TRANSLATION_NAME;
        ?></strong></a></td>
<td></td>
<td></td>
</tr>
<?php foreach($categories as $category){
             ?>
<tr>
<td><?php echo $category["id"];
             ?></td>
<td><?php echo real_htmlspecialchars($category["name"]);
             ?></td>
<td style="text-align:center;"><a href="?action=categories&edit=<?php echo $category["id"];
             ?>"><img src="gfx/edit.gif" alt="<?php echo TRANSLATION_EDIT;
            ?>" title="<?php echo TRANSLATION_EDIT;
            ?>"></td>
<?php if($category["id"] != 1){
                 ?>

<td style="text-align:center;"><a href="?action=categories&del=<?php echo $category["id"];
                 ?>" onclick="return confirm('Wirklich Löschen?')"><img src="gfx/delete.gif" alt="<?php echo TRANSLATION_DELETE;
                ?>" title="<?php echo TRANSLATION_DELETE;
                ?>"></a></td>

<?php }else{
                 ?>
<td style="text-align:center;"><a href="#" onclick="alert('Die Allgemeine Kategorie kann nicht gelöscht werden!')"><img src="gfx/delete.gif" alt="<?php echo TRANSLATION_DELETE;
                ?>" title="<?php echo TRANSLATION_DELETE;
                ?>"></a></td>
<?php }
             ?>
</tr>
<?php }
         ?>
</table>
<?php }else if(isset($_GET["add"])){
         ?>
<h2><?php echo TRANSLATION_CREATE_CATEGORY;
        ?></h2>
<form action="?action=categories" method="post">
<p><?php echo TRANSLATION_NAME;
        ?> <input type="text" name="name" value="">
<p><input type="submit" name="create" value="<?php echo TRANSLATION_CREATE;
        ?>"></p>
</form>

<?php }else if(isset($_GET["edit"])){
         ?>
<h2><?php echo TRANSLATION_EDIT_CATEGORY;
        ?></h2>
<form action="?action=categories" method="post">
<input type="hidden" name="id" value="<?php echo intval($_GET["edit"])?>">
<p><?php echo TRANSLATION_NAME;
        ?> <input type="text" name="name" value="<?php echo categories :: getCategoryById(intval($_GET["edit"]));
         ?>">
<p><input type="submit" name="update" value="<?php echo TRANSLATION_SAVE;
        ?>"></p>
</form>

<?php }
     ?>
<?php }
?>
