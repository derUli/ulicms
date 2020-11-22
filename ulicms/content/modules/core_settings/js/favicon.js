/* global Translation */
/* global bootbox */

$(() => {
    // Enable submit button after file was selected
    $(".btn-primary").prop('disabled', true)
    $("input[name=favicon_upload_file]").change(
            () => $(".btn-primary").prop('disabled', false)
    );
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
                $("#delete-favicon-loading").show();

                $.post(url, (text, status) => {
                    target.closest('td').html('');
                }).fail(() => {
                    $("#delete-favicon-loading").hide();
                    $("#favicon-wrapper").show();
                });
            }
        });

    });
});
