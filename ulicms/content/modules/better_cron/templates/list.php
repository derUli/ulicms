<?php
$cronjobs = BetterCron::getAllCronjobs();

?>
<table class="tablesorter">
	<thead>
		<tr>
			<th><?php translate("name");?></th>
			<th><?php translate("last_run");?></th>
		</tr>
	</thead>
	<tbody>
<?php foreach($cronjobs as $name=>$last_run){?>
<tr>
			<td><?php Template::escape($name);?></td>
			<td><?php Template::escape(strftime("%x %X", $last_run));?></td>
		</tr>
<?php }?>
</tbody>
</table>