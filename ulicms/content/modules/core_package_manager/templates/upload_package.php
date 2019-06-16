<?php
$permissionChecker = new ACL();
if (!$permissionChecker->hasPermission("install_packages")) {
    noPerms();
} else {
    ?>
    <p>
        <a href="<?php echo ModuleHelper::buildActionURL("install_method"); ?>"
           class="btn btn-default btn-back"><i class="fa fa-arrow-left" aria-hidden="true"></i>
            <?php translate("back") ?></a>
    </p>
    <?php
    $temp_folder = ULICMS_DATA_STORAGE_ROOT . DIRECTORY_SEPARATOR . "content" . DIRECTORY_SEPARATOR . "tmp";
    if (!empty($_POST)) {
        if (count($_FILES) > 0) {
            $file_in_tmp = $temp_folder . DIRECTORY_SEPARATOR . $_FILES['file']['name'];
            if (move_uploaded_file($_FILES['file']['tmp_name'], $file_in_tmp)) {
                if (endsWith($file_in_tmp, ".tar.gz")) {
                    $pkg = new PackageManager();
                    if ($pkg->installPackage($file_in_tmp)) {
                        @unlink($file_in_tmp);
                        echo "<p style='color:green'>" . get_translation("PACKAGE_SUCCESSFULL_UPLOADED", array(
                            "%file%" => $_FILES['file']['name']
                        )) . "</p>";
                    } else {
                        echo "<p style='color:red'>" . get_translation("installation_failed", array(
                            "%file%" => $_FILES['file']['name']
                        )) . "</p>";
                    }
                } else if (endsWith($file_in_tmp, ".sin")) {
                    $url = "?action=pkginfo&file=" . basename($file_in_tmp);
                    Request::javascriptRedirect($url);
                } else {
                    echo "<p style='color:red'>" . get_translation("not_supported_format") . "</p>";
                }
            } else {
                echo "<p style='color:red'>" . get_translation("upload_failed") . "</p>";
            }
        }
    }
    ?>
    <h1><?php translate("upload_package"); ?></h1>
    <form action="?action=upload_package" enctype="multipart/form-data"
          method="post">
              <?php
              csrf_token_html();
              ?>
        <input type="file" name="file"><br /> <br />
        <button type="submit" class="btn btn-warning"><i class="fa fa-upload" aria-hidden="true"></i> <?php translate("install_package"); ?></button>
    </form>
    <?php
}
