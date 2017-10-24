<div class="scroll">
	<table class="tablesorter">
		<thead>
			<tr>
				<th class="hide-on-mobile"><?php translate("id");?></th>
				<th><?php translate("username");?></th>
				<th class="hide-on-mobile"><?php translate("lastname");?></th>
				<th class="hide-on-mobile"><?php translate("firstname");?></th>
				<th class="hide-on-mobile"><?php translate("locked");?></th>
			<?php if(ViewBag::get("can_edit")){?>
			<th><?php translate("expire_date");?></th>
			<?php }?>
			<td></td>
			</tr>
		</thead>
		<tbody>
	<?php foreach(ViewBag::get("users") as $user){?>
	<tr>
				<th class="hide-on-mobile"><?php echo $user->getId();?></th>
				<th><?php Template::escape($user->getUsername());?></th>
				<th class="hide-on-mobile"><?php Template::escape($user->getLastname());?></th>
				<th class="hide-on-mobile"><?php Template::escape($user->getFirstname());?></th>
				<td class="hide-on-mobile"><?php echo $user->getLocked() ? translate("yes") : translate("no");?></td>
				<?php
		
		$cssClass = "";
		$expire_date = UserSettings::get ( "expire_date", "int", $user->getId () );
		if ($expire_date and time () > $expire_date) {
			$cssClass = "text-red";
		}
		?>		
			<td class="<?php echo $cssClass;?>"><?php echo $expire_date ? ExpireUsers::formatDate($expire_date) : "-";?></td>
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
</div>
<style type="text/css">
/* include this as inline css since it doesn't make sense to 
create a css file for only one rule
*/
.text-red {
	color: red !important;
}
</style>