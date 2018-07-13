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
				<td><?php esc($controller->getMySQLVersion());?></td>
			</tr>
<?php
$success = file_get_contents_wrapper("https://www.ulicms.de/", true) ? $checkOk : $checkFailed;
$symbol = $success ? $checkOk : $checkFailed;
$yesNo = $success ? get_translation("yes") : get_translation("no");
?>
<tr>
				<td><?php echo $symbol;?></td>
				<td><?php translate("ulicms_services_reachable");?></td>
				<td><?php translate("yes");?></td>
				<td><?php esc($yesNo);?></td>
			</tr>
<?php
$success = file_get_contents_wrapper(ModuleHelper::getBaseUrl("/.txt"), true);
$symbol = $success ? $checkOk : $checkFailed;
$yesNo = $success ? get_translation("yes") : get_translation("no");
?>
<tr>
				<td><?php echo $symbol;?></td>
				<td><?php translate("url_rewriting_working");?></td>
				<td><?php translate("yes");?></td>
				<td><?php esc($yesNo);?></td>
			</tr>
<?php

$requiredExtensions = array(
    "mysqli",
    "gd",
    "json",
    "mbstring",
    "openssl",
    "dom", "xml"
);?>
<?php foreach($requiredExtensions as $extension){?>
<?php 
$success = extension_loaded($extension);
$symbol = $success ? $checkOk : $checkFailed;
$yesNo = $success ? get_translation("yes") : get_translation("no");
?>
<tr>
<td><?php echo $symbol;?></td>
				<td><?php translate("php_extension_x_installed", array("%extension%"=> $extension));?></td>
				<td><?php translate("yes");?></td>
				<td><?php esc($yesNo);?></td>
</tr>
<?php }?>
		</tbody>
	</table>