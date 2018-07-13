<?php
$controller = ControllerRegistry::get("HealthCheckController");

$checkOk = "✓";
$checkFailed = "✗";
$cssOk = "text-center text-success text-green";
$cssFailed = "text-center text-danger text-red";
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
$css = version_compare(phpversion(), "5.6", ">=") ? $cssOk : $cssFailed;
?>
<td class="<?php esc($css)?>" style="width: 30px;"><?php echo $symbol;?></td>
				<td>PHP Version</td>
				<td>5.6</td>
				<td><?php esc(phpversion());?></td>
			</tr>
			<tr>
<?php
$symbol = version_compare($controller->getMySQLVersion(), "5.5.3", '>=') ? $checkOk : $checkFailed;
$css = version_compare($controller->getMySQLVersion(), "5.5.3", '>=') ? $cssOk : $cssFailed;
?>
	    

				<td class="<?php esc($css);?>"><?php echo $symbol;?></td>
				<td>MySQL Version</td>
				<td>5.5.3</td>
				<td><?php esc($controller->getMySQLVersion());?></td>
			</tr>
<?php
$success = file_get_contents_wrapper("https://www.ulicms.de/", true) ? $checkOk : $checkFailed;
$symbol = $success ? $checkOk : $checkFailed;
$yesNo = $success ? get_translation("yes") : get_translation("no");
$css = $success ? $cssOk : $cssFailed;
?>
<tr>
				<td class="<?php esc($css);?>"><?php echo $symbol;?></td>
				<td><?php translate("ulicms_services_reachable");?></td>
				<td><?php translate("yes");?></td>
				<td><?php esc($yesNo);?></td>
			</tr>
<?php
$success = file_get_contents_wrapper(ModuleHelper::getBaseUrl("/.txt"), true);
$symbol = $success ? $checkOk : $checkFailed;
$yesNo = $success ? get_translation("yes") : get_translation("no");
$css = $success ? $cssOk : $cssFailed;
?>
<tr>
				<td class="<?php esc($css);?>"><?php echo $symbol;?></td>
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
    "dom",
    "xml"
);
?>
<?php foreach($requiredExtensions as $extension){?>
<?php
    $success = extension_loaded($extension);
    $symbol = $success ? $checkOk : $checkFailed;
    $yesNo = $success ? get_translation("yes") : get_translation("no");
    $css = $success ? $cssOk : $cssFailed;
    ?>
<tr>

				<td class="<?php esc($css);?>"><?php echo $symbol;?></td>
				<td><?php translate("php_extension_x_installed", array("%extension%"=> $extension));?></td>
				<td><?php translate("yes");?></td>
				<td><?php esc($yesNo);?></td>
			</tr>
<?php }?>
		</tbody>
	</table>
</div>