<?php $result = ViewBag::get("result"); ?>
<table class="tablesorter">
	<thead>
		<tr>
    <?php while($field = Database::fetchField($result)){?>
    <th><?php esc($field->name);?></th>
    <?php }?>
		</tr>
	</thead>
	<tbody>
  <?php
// Write rows
while ($row = Database::fetchRow($result)) {
    ?>
    <tr>
			<?php
    
    foreach ($row as $cell) {
        ?>
			    <td><?php echo nl2br(_esc(!is_null($cell) ? $cell : "[NULL]"));?></td>
			    <?php
    }
    ?>
		</tr>
  <?php } ?></tbody>
</table>