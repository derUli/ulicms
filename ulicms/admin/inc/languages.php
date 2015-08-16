<h2>
<?php
echo TRANSLATION_LANGUAGES;
?>
</h2>
<?php

if (defined ("_SECURITY")){
    
     $acl = new ACL ();
     if ($acl -> hasPermission ("languages")){
        
         $languages = db_query ("SELECT * FROM " . tbname ("languages") . " ORDER BY language_code ASC");
        
         ?>
<form action="index.php?action=languages" method="post">
<?php
        
         csrf_token_html ();
         ?>
	<table border=0>
		<tr>
			<td><strong><?php
        
         echo TRANSLATION_SHORTCODE;
         ?>
			</strong></td>
			<td><input type="text" name="language_code" maxlength=6 size=6></td>
		</tr>
		<tr>
			<td style="width: 100px;"><strong><?php
        
         echo TRANSLATION_FULL_NAME;
         ?>
			</strong></td>
			<td><input type="text" name="name" maxlength=100 size=40></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" name="add_language"
				value="<?php
        
         echo TRANSLATION_ADD_LANGUAGE;
         ?>"></td>
		</tr>

	</table>
</form>
<br>
<div class="seperator"></div>
<br>
<?php
        
         if (db_num_rows ($languages) > 0){
             ?>
<table>
	<tr>
		<td><strong><?php
            
             echo TRANSLATION_SHORTCODE;
             ?></strong></td>
		<td><strong><?php
            
             echo TRANSLATION_FULL_NAME;
             ?></strong></td>
		<td align="center"><strong><?php
            
             echo TRANSLATION_STANDARD;
             ?></strong></td>
		<td></td>
	</tr>
	<?php
             while ($row = db_fetch_object ($languages)){
                 ?>
	<tr id="dataset-<?php echo $row->id;?>">
		<td><?php echo htmlspecialchars($row -> language_code)?>
		</td>
		<td><?php
                
                 echo htmlspecialchars ($row -> name);
                 ?>
		</td>

		<td align="center" style="font-weight: bold;"><?php
                 if ($row -> language_code === getconfig ("default_language")){
                     echo "<span style='color:green !important;'>" . TRANSLATION_YES . "</span>";
                     }else{
                     ?> <a
			onclick="return confirm('<?php
                     echo str_ireplace ("%name%", $row -> name, TRANSLATION_REALLY_MAKE_DEFAULT_LANGUAGE);
                     ?>')"
			href="index.php?action=languages&default=<?php echo $row -> language_code?>">
				<span style="color: red !important;"><?php
                    
                     echo TRANSLATION_NO;
                     ?></span>
		</a> <?php
                     }
                 ?>
		</td>

		<td align="center"><?php
                
                 if ($row -> language_code == getconfig ("default_language")){
                     ?> <a
			onclick="javascript:alert('<?php
                    
                     echo TRANSLATION_CANT_DELETE_DEFAULT_LANGUAGE;
                     ?>')"
			href="#"> <img src="gfx/delete.gif" class="mobile-big-image"
				alt="<?php
                    
                     echo TRANSLATION_DELETE;
                     ?>"
				title="<?php
                    
                     echo TRANSLATION_DELETE;
                     ?>">
		</a> <?php
                     }else{
                     ?> <form
			onsubmit="return confirm('<?php
                     echo str_ireplace ("%name%", $row -> name, TRANSLATION_DELETE_LANGUAGE_REALLY);
                     ?>')"
			action="index.php?action=languages&delete=<?php echo $row -> id?>" class="delete-form" method="post"> <input type="image"
				src="gfx/delete.gif" class="mobile-big-image" alt="Löschen"
				title="Löschen"><?php csrf_token_html();?>
		</form>  <?php
                     }
                 ?>
		</td>



		<?php
                 }
             ?>



</table>

<script type="text/javascript">

var ajax_options = {
  success : function(responseText, statusText, xhr, $form){
  var action = $($form).attr("action");
  var id = url('?delete', action);
  var list_item_id = "dataset-" + id
  var tr = $("tr#" + list_item_id);
  $(tr).fadeOut();
  
  }
 
}

$("form.delete-form").ajaxForm(ajax_options); 
</script>
<?php
             }
         ?>



		<?php
         }else{
         noperms ();
         }
     ?>

	<?php }
 ?>
