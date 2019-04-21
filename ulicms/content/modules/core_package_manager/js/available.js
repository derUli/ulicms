$(function () {
    $("div#loadpkg").slideDown();
    var container = $("div#pkglist");
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
            var url = $(event.target).attr("href");
            bootbox.confirm("RLY?", function (confirmed) {
                if (confirmed) {
                    location.replace(url);
                }
            });
        });
    });
});
