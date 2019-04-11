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
    $packageListURL = $pkg_src . "list.txt";

    $packageList = @file_get_contents_wrapper($packageListURL);

    if ($packageList) {
        $packageList = strtr($packageList, array(
            "\r\n" => PHP_EOL,
            "\r" => PHP_EOL,
            "\n" => PHP_EOL
        ));
        $packageList = explode(PHP_EOL, $packageList);
    }

    if ($packageList) {
        natcasesort($packageList);
        $packageList = array_filter($packageList, 'strlen');
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
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    for ($i = 0; $i < count($packageList); $i ++) {
                        $name = $packageList [$i];
                        $splittedName = $pkgManager->splitPackageName($name);
                        if (count($splittedName) >= 2) {
                            $nameWithoutVersion = $splittedName[0];
                            $version = $splittedName[1];
                        }
                        $descriptionURL = $pkg_src . "descriptions/" . $name . ".txt";
                        $description = @file_get_contents_wrapper($descriptionURL);
                        ?>
                        <tr>
                            <td><?php esc($nameWithoutVersion); ?></td>
                            <td><?php esc($version); ?>
                            </td>
                            <td><?php
                                if (StringHelper::isNullOrWhitespace($description)) {
                                    translate("no_description_available");
                                } else {
                                    echo nl2br($description);
                                }
                                ?>
                            </td>
                            <td>
                                <a href="<?php esc(ModuleHelper::buildActionURL("install_modules", "packages={$name}")); ?>" class="btn btn-primary">
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
