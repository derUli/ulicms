<?php
function count_by_category_render() {
	ob_start();
	$sql = "select c.name as Kategorie, count(e.id) as Anzahl from {prefix}content e inner join {prefix}categories c on c.id = category where type = 'article'  group by e.category order by Kategorie asc;";
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
	<tr>
			<td><?php Template::escape($row->Kategorie);?></td>
			<td class="text-right"><?php  Template::escape(strval($row->Anzahl));?></td>
		</tr>
	<?php }?>
	</tbody>

</table>

<?php
    return ob_get_clean();
}