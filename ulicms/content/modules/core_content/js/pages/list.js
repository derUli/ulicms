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

    $("#show_filters").change((event) => {
        const url = $(event.target).data("url");
        $(".filters").slideToggle();

        $.ajax({
            method: "get",
            url: url,
            error: (xhr) =>
                alert(xhr.responseText)
        });
    });
});