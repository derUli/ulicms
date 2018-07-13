<?php
$controller = ControllerRegistry::get("HealthCheckController");

$checkOk = "✓";
$checkFailed = "✗";
?>
<div class="scroll">
	<table style="width: 100%;">
		<thead>
			<tr>

				<th></th>
				<th><?php translate("object");?></th>
				<th><?php translate("expected");?></th>
				<th><?php translate("actual");?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
<?php
$symbol = version_compare(phpversion(), "5.6", ">=") ? $checkOk : $checkFailed;
?>
<td><?php echo $symbol;?></td>
				<td>PHP Version</td>
				<td><?php esc(phpversion());?></td>
				<td>5.6</td>
			</tr>
			<tr>
<?php
$symbol = version_compare($controller->getMySQLVersion(), "5.5.3", '>=') ? $checkOk : $checkFailed;
?>
	    

<td><?php echo $symbol;?></td>
				<td>MySQL Version</td>
				<td>5.5.3</td>
				<td><?php esc($controller->getMySQLVersion);?></td>
			</tr>

		</tbody>
	</table>