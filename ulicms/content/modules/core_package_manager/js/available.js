$(function () {
    $("div#loadpkg").slideDown();
    const container = $("div#pkglist");
    $.get(container.data("url"), function (result) {
        $("div#loadpkg").slideUp();
        $(container).html(result);
        $(container).slideDown();
        initRemoteAlerts(container);
        $(container).find(".tablesorter").DataTable({
            language: {
                url: $("body").data("datatables-translation")
            },
            columnDefs: [{targets: "no-sort", orderable: false}]
        });
        $(container).find(".btn-install").click(function (event) {
            event.preventDefault();
            const url = $(event.target).attr("href");
            const name = $(event.target).data("name");
            const message = AvailablePackageTranslation.AskForInstallPackage.replace
            ("%pkg%", name);
            bootbox.confirm(message, function (confirmed) {
                if (confirmed) {
                    location.replace(url);
                }
            });
        });
    });
});
