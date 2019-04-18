<?php
// TODO: Refactor Code
// This file should not contain any business logic.
// It should only do output
// Implement a backend action which fetches the index of the package source

$permissionChecker = new ACL ();
if (!$permissionChecker->hasPermission("install_packages")) {
    noPerms();
    ?>
    <?php
}

$pkg_src = Settings::get("pkg_src");
@set_time_limit(0);

if (!$pkg_src) {
    ?>
    <p>
        <strong><?php translate("error"); ?> </strong> <br />
        <?php
        translate("pkgsrc_not_defined");
        ?>
    </p>
    <?php
} else {
    $version = new UliCMSVersion ();
    $internalVersion = implode(".", $version->getInternalVersion());
    $pkg_src = str_replace("{version}", $internalVersion, $pkg_src);

    $pkgManager = new PackageManager();
	
	// Proof of Concept: Paketquellen Index in einer einzigen Datei als JSON
	// TODO: "Vernünftig" implementieren d.H. objektorientiert und mit Unit Tests
	
    $packageListURL = $pkg_src . "index.json";
    $packageList = @file_get_contents_wrapper($packageListURL);
	
    if ($packageList) {
        $packageList = json_decode($packageList);
    }
	
    if (!$packageList or count($packageList) === 0) {
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
                        <th class="no-sort"></td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach($packageList as $package){
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
                                <a href="<?php esc(ModuleHelper::buildActionURL("install_modules", "packages={$package->name}-{$package->version}")); ?>" class="btn btn-primary">
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
