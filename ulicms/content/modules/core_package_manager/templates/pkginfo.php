<?php
$permissionChecker = new ACL();
if (!$permissionChecker->hasPermission("install_packages")) {
    noPerms();
} else {
    if (StringHelper::isNotNullOrEmpty($_REQUEST["file"]) and endsWith($_REQUEST["file"], ".sin")) {
        $tempfile = Path::resolve("ULICMS_TMP/" . basename($_REQUEST["file"]));
        if (is_file($tempfile)) {
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
            $size = NumberFormatHelper::formatSizeUnits($size);
            ?>
            <p>
                <a href="<?php echo ModuleHelper::buildActionURL("upload_package"); ?>"
                   class="btn btn-default btn-back"><i class="fa fa-arrow-left"></i> <?php translate("back") ?></a>
            </p>
            <h1><?php
                Template::escape($id);
                ?></h1>
            <table>
                <?php
                if ($name) {
                    ?>
                    <tr>
                        <td><strong><?php translate("name") ?></strong></td>
                        <td><?php Template::escape($name) ?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td><strong><?php translate("version") ?></strong></td>
                    <td><?php Template::escape($version) ?></td>
                </tr>
                <tr>
                    <td><strong><?php translate("size") ?></strong></td>
                    <td><?php Template::escape($size) ?></td>
                </tr>
                <?php
                if ($build_date) {
                    ?>
                    <tr>
                        <td><strong><?php translate("build_date") ?></strong></td>
                        <td><?php Template::escape(strftime("%x %X", $build_date)); ?></td>
                    </tr>
                <?php } ?>
                <?php
                if ($screenshot) {
                    ?>
                    <tr>
                        <td></td>
                        <td><img src="data:<?php Template::escape($screenshot); ?>"
                                 alt="Screenshot" class="responsive-image"></td>
                    </tr>
                <?php } ?>
                <?php
                if ($description) {
                    ?>
                    <tr>
                        <td><strong><?php translate("description") ?></strong></td>
                        <td><?php Template::escape($description); ?></td>

                    </tr>
                <?php } ?>
                <?php
                if ($compatible_from) {
                    ?>
                    <tr>
                        <td><strong><?php translate("compatible_from") ?></strong></td>
                        <td>UliCMS <?php Template::escape($compatible_from); ?></td>

                    </tr>
                <?php } ?>
                <?php
                if ($compatible_to) {
                    ?>
                    <tr>
                        <td><strong><?php translate("compatible_to") ?></strong></td>
                        <td>UliCMS <?php Template::escape($compatible_to); ?></td>
                    </tr>
                <?php } ?>
                <?php
                if ($min_php_version) {
                    ?>
                    <tr>
                        <td><strong><?php translate("min_php_version") ?></strong></td>
                        <td><?php Template::escape($min_php_version); ?></td>
                    </tr>
                <?php } ?>
                <?php
                if ($max_php_version) {
                    ?>
                    <tr>
                        <td><strong><?php translate("max_php_version") ?></strong></td>
                        <td><?php Template::escape($max_php_version); ?></td>
                    </tr>
                <?php } ?>
                <?php if ($required_php_extensions) { ?>
                    <tr>
                        <td><strong><?php translate("required_php_extensions") ?></strong></td>
                        <td><?php
                            foreach ($required_php_extensions as $extension) {
                                ?>
                                <?php Template::escape($extension); ?><br />
                            <?php } ?></td>
                    </tr>
                    <?php
                }
                ?>
                <?php
                if ($min_mysql_version) {
                    ?>
                    <tr>
                        <td><strong><?php translate("min_mysql_version") ?></strong></td>
                        <td><?php Template::escape($min_mysql_version); ?></td>
                    </tr>
                <?php } ?>

                <?php
                if ($max_mysql_version) {
                    ?>
                    <tr>
                        <td><strong><?php translate("max_mysql_version") ?></strong></td>
                        <td><?php Template::escape($max_mysql_version); ?></td>
                    </tr>
                <?php } ?>
                <?php
                if ($dependencies) {
                    ?>
                    <tr>
                        <td><strong><?php translate("dependencies") ?></strong></td>
                        <td><?php
                            foreach ($dependencies as $dep) {
                                ?>
                                <?php Template::escape($dep); ?><br />
                            <?php } ?></td>
                    </tr>
                <?php } ?>
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