/* global Translation */
/* global bootbox */

$(() => {
    // Enable submit button after file was selected
    $(".btn-primary").prop('disabled', true)
    $("input[name=logo_upload_file]").change(
            () => $(".btn-primary").prop('disabled', false)
    );
    // Delete Logo
    // show load spinner
    // then empty the table column
    $("#delete-logo").click((event) => {
        const target = $(event.target);
        const url = target.data("url");
        bootbox.confirm(
                `${Translation.DeleteLogo}?`, (result) => {
            if (result) {
                $("#logo-wrapper").hide();
                $("#delete-logo-loading").show();
                $.post(url)
                        .done((text, status) => {
                            target.closest('tr').remove();
                            vanillaToast.success(Translation.LogoDeleted);
                        })
                        .fail(() => {
                            $("#delete-logo-loading").hide();
                            $("#logo-wrapper").show();
                        });
            }
        });

    });
});
