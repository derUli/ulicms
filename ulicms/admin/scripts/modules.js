/* global Translation */

uninstallModule = (url, name) => {
    if (confirm(Translation.AskForUninstallPackage.replace("%name%", name))) {
        $.ajax({
            url: url,
            success: () => {
                $("li#dataset-module-" + name).slideUp();
            }
        });
    }
    return false;
};
uninstallTheme = (url, name) => {
    if (confirm(Translation.AskForUninstallPackage.replace("%name%", "theme-"
            + name))) {
        $.ajax({
            url: url,
            success: () =>
                $("li#dataset-theme-" + name).slideUp()
        });
    }
    return false;
};
$("form#truncate_installed_patches").ajaxForm(
        {
            success: () =>
                $("div#inst_patch_slide_container").slideUp()
        });