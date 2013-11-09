<?php 
if(!defined("ULICMS_ROOT"))
   die("Dummer Hacker!");

$id = intval($_REQUEST["edit"]);
$acl = new ACL();
$all_permissions = $acl->getPermissionQueryResult($id);
$groupName = real_htmlspecialchars($all_permissions["name"]);
$all_permissions_all = $acl->getDefaultACL(false, true);
$all_permissions = json_decode($all_permissions["permissions"]);
$all_permissions = (array) $all_permissions;
      foreach($all_permissions_all as $name => $value){
            if(!isset($all_permissions[$name]))
               $all_permissions[$name] = $value; 
   }
   

   ksort($all_permissions);

if($all_permissions){

?>
<form action="?action=groups" method="post">
<input type="hidden" name="id" value="<?php echo $id;?>">
<p><strong>Name</strong> <input type="text" name="name" value="<?php echo $groupName;?>"></p>
<p><strong>Berechtigungen:</strong></p>
<fieldset>
<p><input type="checkbox" class="checkall"> Alles auswählen</p>
<p>
<?php foreach($all_permissions As $key => $value){?>
<input type="checkbox" name="user_permissons[]" value="<?php echo $key;?>" <?php if($value) {
echo "checked='checked'";
}?>> <?php echo $key; ?><br/>
<?php }?>
</p>
</fieldset>
<br/>
<input type="submit" value="Speichern" name="edit_group">
</form>

<script type="text/javascript">
$(function () {
    $('.checkall').on('click', function () {
        $(this).closest('fieldset').find(':checkbox').prop('checked', this.checked);
    });
});
</script>
<?php }

else{
?>
<p style="color:red">Diese Gruppe ist nicht vorhanden.</p>
<?php
}