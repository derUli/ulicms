<?php
if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

use UliCMS\Helpers\NumberFormatHelper;
use UliCMS\Packages\SinPackageInstaller;
use UliCMS\Security\Permissions\PermissionChecker;

$permissionChecker = new PermissionChecker(get_user_id());
if (!$permissionChecker->hasPermission("install_packages")) {
    noPerms();
} else {
    if (StringHelper::isNotNullOrEmpty($_REQUEST["file"]) &&
            (endsWith($_REQUEST["file"], ".sin") || endsWith($_REQUEST["file"], ".sin2"))) {
        $tempfile = Path::resolve("ULICMS_TMP/" . basename($_REQUEST["file"]));
        if (file_exists($tempfile)) {
            $pkg = new SinPackageInstaller($tempfile);
            $installable = $pkg->isInstallable();
            $errors = $pkg->getErrors();

            $id = $pkg->getProperty("id");
            $version = $pkg->getProperty("version");
            $name = $pkg->getProperty("name");
            $description = $pkg->getProperty("description");
            $compatible_from = $pkg->getProperty("compatible_from");
            $compatible_to = $pkg->getProperty("compatible_to");
            $min_php_version = $pkg->getProperty("min_php_version");
            $max_php_version = $pkg->getProperty("max_php_version");
            $min_mysql_version = $pkg->getProperty("min_mysql_version");
            $max_mysql_version = $pkg->getProperty("max_mysql_version");
            $required_php_extensions = $pkg->getProperty("required_php_extensions");
            $dependencies = $pkg->getProperty("dependencies");
            $license = $pkg->getProperty("license");
            $build_date = $pkg->getProperty("build_date");
            $screenshot = $pkg->getProperty("screenshot");
            $size = intval($pkg->getSize());
            ?>
            <p>
                <a href="<?php echo ModuleHelper::buildActionURL("upload_package"); ?>"
                   class="btn btn-default btn-back is-not-ajax"><i class="fa fa-arrow-left"></i> <?php translate("back") ?></a>
            </p>
            <h1><?php esc($id); ?></h1>
            <table>
                <?php
                if ($name) {
                    ?>
                    <tr>
                        <td><strong><?php translate("name") ?></strong></td>
                        <td><?php esc($name) ?></td>
                    </tr>
                <?php }
                ?>
                <tr>
                    <td><strong><?php translate("version") ?></strong></td>
                    <td><?php esc($version) ?></td>
                </tr>
                <tr>
                    <td><strong><?php translate("size") ?></strong></td>
                    <td><?php echo NumberFormatHelper::formatSizeUnits($size); ?></td>
                </tr>
                <?php
                if ($build_date) {
                    ?>
                    <tr>
                        <td><strong><?php translate("build_date") ?></strong></td>
                        <td><?php esc(PHP81_BC\strftime("%x %X", $build_date)); ?></td>
                    </tr>
                <?php }
                ?>
                <?php
                if ($screenshot) {
                    ?>
                    <tr>
                        <td></td>
                        <td><img src="data:<?php esc($screenshot); ?>"
                                 alt="Screenshot" class="img-responsive"></td>
                    </tr>
                <?php }
                ?>
                <?php
                if ($description) {
                    ?>
                    <tr>
                        <td><strong><?php translate("description") ?></strong></td>
                        <td><?php esc($description); ?></td>

                    </tr>
                <?php }
                ?>
                <?php
                if ($compatible_from) {
                    ?>
                    <tr>
                        <td><strong><?php translate("compatible_from") ?></strong></td>
                        <td>UliCMS <?php esc($compatible_from); ?></td>

                    </tr>
                <?php }
                ?>
                <?php
                if ($compatible_to) {
                    ?>
                    <tr>
                        <td><strong><?php translate("compatible_to") ?></strong></td>
                        <td>UliCMS <?php esc($compatible_to); ?></td>
                    </tr>
                <?php }
                ?>
                <?php
                if ($min_php_version) {
                    ?>
                    <tr>
                        <td><strong><?php translate("min_php_version") ?></strong></td>
                        <td><?php esc($min_php_version); ?></td>
                    </tr>
                <?php }
                ?>
                <?php
                if ($max_php_version) {
                    ?>
                    <tr>
                        <td><strong><?php translate("max_php_version") ?></strong></td>
                        <td><?php esc($max_php_version); ?></td>
                    </tr>
                <?php }
                ?>
                <?php if ($required_php_extensions) { ?>
                    <tr>
                        <td><strong><?php translate("required_php_extensions") ?></strong></td>
                        <td><?php
                foreach ($required_php_extensions as $extension) {
                        ?>
                                <?php esc($extension); ?><br />
                            <?php }
                            ?></td>
                    </tr>
                <?php }
                ?>
                <?php
                if ($min_mysql_version) {
                    ?>
                    <tr>
                        <td><strong><?php translate("min_mysql_version") ?></strong></td>
                        <td><?php esc($min_mysql_version); ?></td>
                    </tr>
                <?php }
                ?>

                <?php
                if ($max_mysql_version) {
                    ?>
                    <tr>
                        <td><strong><?php translate("max_mysql_version") ?></strong></td>
                        <td><?php esc($max_mysql_version); ?></td>
                    </tr>
                <?php }
                ?>
                <?php
                if ($dependencies) {
                    ?>
                    <tr>
                        <td><strong><?php translate("dependencies") ?></strong></td>
                        <td><?php
                foreach ($dependencies as $dep) {
                        ?>
                                <?php esc($dep); ?><br />
                            <?php }
                            ?></td>
                    </tr>
                <?php }
                ?>
            </table>
            <?php if ($license) { ?>
                <h2><?php translate("license_agreement"); ?></h2>
                <div class="license-agreement"><?php echo nl2br(Template::getEscape($license)) ?></div>
            <?php } ?>
            <?php
            if (!$installable) {
                ?>
                <h2><?php translate("errors"); ?></h2>
                <?php
                echo implode("<br/>", $errors);
            }
            ?>
            <?php
            if ($installable) {
                ?>
                <div class="text-right" style="margin-top: 30px;">
                    <?php
                    echo ModuleHelper::buildMethodCallForm("PkgInfoController", "install", array(
                        "file" => basename($tempfile)
                    ));
                    ?>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-box"></i> <?php translate("install"); ?></button>
                </form>
                </div>
                <?php
            }
        } else {
            translate("file_not_found", array(
                "%file%" => $tempfile
            ));
        }
    }
}
