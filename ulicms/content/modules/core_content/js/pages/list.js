/* global Translation, bootbox */

$(() => {
    // confirmation on empty trash
    $("a#empty-trash").click((event) => {
        const item = $(event.currentTarget);
        const href = $(item).attr("href");
        event.preventDefault();
        bootbox.confirm(Translation.WannaEmptyTrash, (result) => {
            if (result) {
                location.replace(href);
            }
        });
    });

    // fetch updated results after filter values where changed
    $(".filters select").change((event) => {
        const target = event.target;
        const dataTable = $(".tablesorter").DataTable();
        dataTable.ajax.reload();

        if ($(target).is("#filter_language") ||
                $(target).is("#filter_menu")) {
            loadParentPages();
        }
    });

    // "Show Filters switch
    $("#show_filters").change((event) => {
        const url = $(event.target).data("url");
        const isChecked = $(event.target).is(':checked');
        if (isChecked) {
            $(".filters").slideDown();
        } else {
            $(".filters").slideUp();
        }

        $.ajax({
            method: "get",
            url: url,
            error: (xhr) =>
                alert(xhr.responseText)
        });
    });

    loadParentPages();
});

// filter parent pages by selected language and menu
const loadParentPages = () => {
    const data = {
        csrf_token: $("input[name=csrf_token]")
                .first()
                .val(),
        language: $("#filter_language").val(),
        menu: $("#filter_menu").val()
    };

    const url = $(".filter-wrapper")
            .first()
            .data("parent-pages-url");
    $.get(url, data, function (text, status) {
        $("#filter_parent").html(text);
    });
};