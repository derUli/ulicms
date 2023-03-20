/* global Translation */

const uninstallModule = (url, name) => {
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

const uninstallTheme = (url, name) => {
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