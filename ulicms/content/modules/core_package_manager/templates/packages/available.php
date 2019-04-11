<?php

use UliCMS\Security\PermissionChecker;

$permissionChecker = new PermissionChecker(get_user_id());
if (!$permissionChecker->hasPermission("install_packages")) {
    noPerms();
} else {
    ?>
    <p>
        <a href="<?php echo ModuleHelper::buildActionURL("install_method"); ?>"
           class="btn btn-default btn-back"><i class="fa fa-arrow-left" aria-hidden="true"></i> <?php translate("back") ?></a>
    </p>
    <h1><?php translate("available_packages") ?></h1>
    <div id="loadpkg">
        <img style="margin-right: 15px; float: left;" src="gfx/loading.gif"
             alt="Bitte warten...">
        <div style="padding-top: 3px;">
            <?php translate("loading_data"); ?>
        </div>
    </div>
    <div id="pkglist" data-url="<?php echo ModuleHelper::buildMethodCallUrl(PackageController::class, "availablePackages"); ?>"></div>
    <script type="text/javascript">

        // TODO: Move this to external js file
        $(function () {
            $("div#loadpkg").slideDown();
            var container = $("div#pkglist");
            $.get(container.data("url"), function (result) {
                $("div#loadpkg").slideUp();
                $(container).html(result);
                $(container).slideDown();
                $(container).find(".tablesorter").DataTable({
                    language: {
                        url: $("body").data("datatables-translation")
                    },
                    columnDefs: [{targets: "no-sort", orderable: false}]
                });
            });
        });
    </script>
    <?php
}