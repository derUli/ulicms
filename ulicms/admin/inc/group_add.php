<?php
if (! defined ("ULICMS_ROOT")){
     die ("Dummer Hacker!");
    }

$acl = new ACL ();

$all_permissions = $acl -> getDefaultACL (true, true);

?>
<form action="?action=groups" method="post">
<?php

csrf_token_html ();
?>
	<p>
		<strong><?php

 echo TRANSLATION_NAME;
 ?> </strong> <input type="text" required="true" name="name" value="">
	</p>
	<p>
		<strong><?php

 echo TRANSLATION_PERMISSIONS;
 ?> </strong>
	</p>
	<fieldset>
		<p>
			<input id="checkall" type="checkbox" class="checkall"> <label
				for="checkall"><?php

 echo TRANSLATION_SELECT_ALL;
 ?> </label>
		</p>
		<p>
		<?php

 foreach ($all_permissions as $key => $value){
     ?>
			<input type="checkbox" id="<?php
    
     echo $key;
     ?>"
				name="user_permissons[]" value="<?php
    
     echo $key;
     ?>"> <label for="<?php
    
     echo $key;
     ?>"><?php
    
     echo $key;
     ?> </label> <br />
<?php
     }
 ?>
		</p>
	</fieldset>
	<br /> <input type="submit"
		value="<?php

 echo TRANSLATION_CREATE_GROUP;
 ?>"
		name="add_group">
</form>

<script type="text/javascript">
$(function () {
    $('.checkall').on('click', function () {
        $(this).closest('fieldset').find(':checkbox').prop('checked', this.checked);
    });
});
</script>

<?php
if (getconfig ("override_shortcuts") == "on" || getconfig ("override_shortcuts") == "backend"){
     ?>
<script type="text/javascript" src="scripts/ctrl-s-submit.js">
</script>
<?php
    
    }
?>