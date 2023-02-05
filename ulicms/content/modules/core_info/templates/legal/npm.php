<?php
include_once dirname(__FILE__) . "/icons.php";
$controller = new InfoController();

$legalInfo = $controller->_getNpmLegalInfo();
?>
<h1><?php translation("legal_npm"); ?></h1>
<table class="tablesorter" style="width:100%">
    <thead>
        <tr>
            <th><?php translate("package"); ?></th>
            <th><?php translate("license_type"); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($legalInfo as $package) {
            ?>
            <tr>
                <td>
                    <?php esc($package->name); ?>
                </td>

                <td>
                    <?php esc($package->licenseType); ?>
                </td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</tfoot>
</table>
