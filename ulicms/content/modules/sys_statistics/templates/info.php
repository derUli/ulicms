<?php
$countByType = ViewBag::get ( "count_by_type" );
$languages = getAllUsedLanguages ();
?>
<p>
<form action="index.php" method="get" id="sys_statistics_language">
	<input type="hidden" name="action" value="module_settings"> <input
		type="hidden" name="module" value="sys_statistics">
	<h4><?php translate("language");?></h4>
	<select name="language">
		<option value="">[<?php translate("every");?>]</option>
		<?php foreach($languages as $language){?>
		<option value="<?php Template::escape($language);?>"
			<?php if(Request::getVar("language") == $language) echo " selected";?>><?php Template::escape(getLanguageNameByCode($language));?></option>
<?php }?>

	
	</select>
</form>
</p>
<div class="voffset4">
	<table class="tablesorter">
		<thead>
			<tr>
				<th><?php translate("type");?></th>
				<th><?php translate("count");?></th>
			</tr>
		</thead>
		<tbody>
<?php foreach($countByType as $name=>$count){?>
<tr>
				<td><?php translate($name);?></td>
				<td class="text-right"><?php echo $count;?></td>
			</tr>
<?php }?>
</tbody>
	</table>
</div>
<script type="text/javascript"
	src="<?php echo ModuleHelper::buildModuleRessourcePath("sys_statistics", "js/general.js");?>"></script>