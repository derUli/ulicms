<?php
if (! function_exists ( "getURLForCategory" )) {
	function getURLForCategory($category_id) {
		$firstInCategory = ModuleHelper::getMainController ( "first_in_category" );
		$data = $firstInCategory->getFirstListWithCategory ( intval ( $category_id ), "de" );
		if ($data->id) {
			return buildSEOUrl ( $data->systemname );
		} else {
			return null;
		}
	}
}
function count_by_category_render() {
	ob_start ();
	$sql = "select c.name as Kategorie, c.id as kid, count(e.id) as Anzahl from {prefix}content e inner join {prefix}categories c on c.id = category where type = 'article'  group by e.category order by Kategorie asc;";
	$query = Database::query ( $sql, true );
	?>
<table class="tablesorter">

	<thead>
		<tr>
			<th>Kategorie</th>
			<th>Anzahl</th>
		</tr>
	</thead>
	<tbody>
	<?php while($row = Database::fetchObject($query)){?>
	<?php $url = getURLForCategory($row->kid);?>
	<tr>
			<td><?php if($url){?><a href="<?php Template::escape($url)?>">
			<?php Template::escape($row->Kategorie);?>
			</a><?php } else {?>
			<?php Template::escape($row->Kategorie);?>
			<?php }?>
			</td>
			<td class="text-right"><?php  Template::escape(strval($row->Anzahl));?></td>
		</tr>
	<?php }?>
	</tbody>

</table>

<?php
	return ob_get_clean ();
}