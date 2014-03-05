<?php
if(!defined("ULICMS_ROOT"))
     die("Dummer Hacker!");

$acl = new ACL();

$all_permissions = $acl -> getDefaultACL(true, true);

?>
<form action="?action=groups" method="post">
<p><strong>Name</strong> <input type="text" name="name" value=""></p>
<p><strong>Berechtigungen:</strong></p>
<fieldset>
<p><input id="checkall" type="checkbox" class="checkall"> <label for="checkall">Alles auswählen</label></p>
<p>
<?php foreach($all_permissions As $key => $value){
     ?>
<input type="checkbox" id="<?php echo $key;
     ?>" name="user_permissons[]" value="<?php echo $key;
     ?>"> <label for="<?php echo $key;
     ?>"><?php echo $key;
    ?></label>
   <br/>
<?php }
?>
</p>
</fieldset>
<br/>
<input type="submit" value="anlegen" name="add_group">
</form>

<script type="text/javascript">
$(function () {
    $('.checkall').on('click', function () {
        $(this).closest('fieldset').find(':checkbox').prop('checked', this.checked);
    });
});
</script>

<?php
 if(getconfig("override_shortcuts") == "on" || getconfig("override_shortcuts") == "backend"){
     ?>
<script type="text/javascript" src="scripts/ctrl-s-submit.js">
</script>
<?php }
 ?>