<?php

// TODO: Refactor Code
// This file should not contain any business logic.
// It should only do output
// Implement a backend action which fetches the index of the package source
use UliCMS\Services\Connectors\PackageSourceConnector;

$permissionChecker = new ACL ();
if ($permissionChecker->hasPermission("install_packages")) {


    if (!Settings::get("pkg_src")) {
        ?>
        <p>
            <strong><?php translate("error"); ?> </strong> <br />
            <?php
            translate("pkgsrc_not_defined");
            ?>
        </p>
        <?php
    } else {
        $packageSource = new PackageSourceConnector();
        $fetch = $packageSource->fetch();
        $packages = $packageSource->getAllAvailablePackages();

        if (!$fetch or count($packages) === 0) {
            ?>
            <p>
                <strong><?php translate("error"); ?> </strong> <br />
                <?php translate("no_packages_available"); ?>
            </p>
            <?php
        } else {
            ?>
            <div class="scroll">
                <table class="tablesorter">
                    <thead>
                        <tr>
                            <th><?php translate("package"); ?></th>
                            <th><?php translate("version"); ?></th>
                            <th><?php translate("description"); ?></th>
                            <th class="no-sort"></th>
                            <th class="no-sort"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($packages as $package) {
                            ?>
                            <tr>
                                <td><?php esc($package->name); ?></td>
                                <td><?php esc($package->version); ?></td>
                                <td><?php
                                    echo StringHelper::isNotNullOrWhitespace($package->description) ?
                                            nl2br($package->description) :
                                            get_translation("no_description_available");
                                    ?>
                                </td>
                                <td>
                                    <a href="#" class="btn btn-info btn-sm remote-alert icon"
                                       title="<?php translate("info"); ?>"
                                       data-url="<?php echo ModuleHelper::buildMethodCallUrl(PackageController::class, "getPackageLicense", "name={$package->name}"); ?>">
                                        <i class="fas fa-balance-scale"></i>
                                        <?php translate("license"); ?></a>
                                </td>
                                <td>
                                    <a href="<?php esc(ModuleHelper::buildActionURL("install_modules", "packages={$package->name}-{$package->version}")); ?>"
                                       data-name="<?php esc("{$package->name} {$package->version}"); ?>"
                                       class="btn btn-primary btn-install">
                                        <i class="fas fa-download"></i> <?php translate("install"); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php
        }
    }
} else {
    noPerms();
}