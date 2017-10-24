<table class="tablesorter">
	<thead>
		<tr>
			<th><?php translate("id");?></th>
			<th><?php translate("username");?></th>
			<th><?php translate("lastname");?></th>
			<th><?php translate("firstname");?></th>
			<?php if(ViewBag::get("can_edit")){?>
			<th><?php translate("expire_date");?></th>
			<?php }?>
			<td></td>
		</tr>
	</thead>
	<tbody>
	<?php foreach(ViewBag::get("users") as $user){?>
	<tr>
			<th><?php echo $user->getId();?></th>
			<th><?php Template::escape($user->getUsername());?></th>
			<th><?php Template::escape($user->getLastname());?></th>
			<th><?php Template::escape($user->getFirstname());?></th>
			<?php
		
		$cssClass = "";
		$expire_date = UserSettings::get ( "expire_date", "int", $user->getId () );
		if ($expire_date and time () > $expire_date) {
			$cssClass = "text-red";
		}
		?>
			<th class="<?php echo $cssClass;?>"><?php echo $expire_date ? ExpireUsers::formatDate($expire_date) : "-";?></th>
			<?php if(ViewBag::get("can_edit")){?>
			<td class="text-center"><a
				href="<?php echo ModuleHelper::buildActionURL("edit_expire_user", "id={$user->getId()}");?>">
					<img src="gfx/edit.png" alt="<?php translate("edit");?>"
					title="<?php translate("edit");?>">
			</a></td>
			<?php }?>
		</tr>
	<?php }?>
	</tbody>

</table>

<style type="text/css">
/* include this as inline css since it doesn't make sense to 
create a css file for only one rule
*/
.text-red {
	color: red;
}
</style>