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
