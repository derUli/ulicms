<p>
	<a href="<?php echo ModuleHelper::buildActionURL("code_new");?>"
		class="btn btn-default"><?php translate("new");?></a>
</p>
<table class="tablesorter">
	<thead>
		<tr>
			<th><?php translate("name");?></th>
			<td><strong><?php translate("shortcode");?></strong></td>
			<td></td>
			<td></td>
		</tr>
	</thead>
	<?php $data = is_array(ViewBag::get("datasets")) ? ViewBag::get("datasets") : array(); ?>
	<tbody>
	<?php foreach($data as $ds){?>
		<tr>
			<!-- @FIXME: Inline-Javascripts in externe Datei auslagern. -->
			<td><?php Template::escape($ds->name);?></td>
			<td><input type="text" onclick="this.select();"
				value="[code id=<?php Template::escape($ds->id);?>]" readonly</td>
			<td><img src="gfx/edit.png" alt="<?php translate("edit");?>"></td>
			<td>
				<!--  @FIXME: Sicherheitsabfrage beim Löschen einbauen -->
				<form
					action="<?php Template::escape(ModuleHelper::buildMethodCall("HighlightPHPCode", "deleteCode"));?>"
					method="post"><?php csrf_token_html();?>
					<input type="hidden" name="id"
						value="<?php Template::escape($ds->id)?>"> <input type="image"
						alt="<?php translate("delete")?>" src="gfx/delete.gif">
				</form>
			</td>
		</tr>
		<?php }?>
	</tbody>
</table>