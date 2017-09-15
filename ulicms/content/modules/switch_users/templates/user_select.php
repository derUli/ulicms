<?php
$manager = new UserManager ();
$users = $manager->getAllUsers ();
echo ModuleHelper::buildMethodCallForm ( "SwitchUsers", "switchUser", array (
		"url" => getCurrentURL () 
), "post" );
?>
<strong><?php translate("switch_user")?></strong>
<br />
<select name="user_id" id="user_switch">
<?php foreach($users as $user){?>
<option value="<?php echo $user->getId();?>"
		<?php if(get_user_id() == $user->getId()){ echo "selected";}?>><?php Template::escape($user->getUsername());?></option>
<?php }?>
</select>
</form>
<script type="text/javascript">
$(function(){
	$("#user_switch").change(function(){
			$(this).closest("form").submit();
		});
});
</script>
<style type="text/css">
#user_switch {
	margin-bottom: 20px;
}
</style>