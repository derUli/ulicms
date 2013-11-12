<?php
$acl = new ACL();
if(!is_admin() and !$acl -> hasPermission("categories")){
     noperms();
    
    }else{
    
    if(isset($_GET["del"])){
       $del = intval($_GET["del"]);
       if($del != 1){
         categories::deleteCategory($del);
       }
    }
    
    include_once ULICMS_ROOT.DIRECTORY_SEPERATOR."lib".DIRECTORY_SEPERATOR."string_functions.php";
    $categories = categories::getAllCategories();
}

?>

<?php 
if(count($categories) > 0){
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
<td><?php echo $categories["id"];?></td>
<td><?php echo real_htmlspecialchars($categories["name"]);?></td>
<td>[Bearbeiten]</td>
<td>[Löschen]</td>
</tr>
<?php }?>
</table>
<?php }?>
