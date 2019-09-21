/* global bootbox, AvailablePackageTranslation */

$(() => {
    $("div#loadpkg").slideDown();
    const container = $("div#pkglist");
    $.get(container.data("url"), (result) => {
        $("div#loadpkg").slideUp();
        $(container).html(result);
        $(container).slideDown();
        initRemoteAlerts(container);
        initDataTables("#pkglist");

        $(container).find(".btn-install").click((event) => {
            event.preventDefault();
            const url = $(event.currentTarget).attr("href");
            const name = $(event.currentTarget).data("name");
            const message = AvailablePackageTranslation.AskForInstallPackage
                    .replace
                    ("%pkg%", name);
            bootbox.confirm(message, (confirmed) => {
                if (confirmed) {
                    location.replace(url);
                }
            });
        });
    });
});
