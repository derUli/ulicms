/* global Translation */
/* global bootbox */

$(() => {
    // Delete Favicon
    // show load spinner
    // then empty the table column
    $("#delete-favicon").click((event) => {
        const target = $(event.target);
        const url = target.data("url");
        bootbox.confirm(
                `${Translation.DeleteFavicon}?`, (result) => {
            if (result) {
                $("#favicon-wrapper").hide();
                $("#favicon-wrapper").hide();
                $("#delete-favicon-loading").show();

                $.post(url, (text, status) => {
                    target.closest('td').html('');
                });
            }
        });

    });
});
