/* global Translation */

// scripts for meta description settings page
$(() => {
    $("#error_pages_form").ajaxForm(
            {
                beforeSubmit: () => {
                    $("#message").html("");
                    $("#loading").show();
                },
                success: () => {
                    $("#loading").hide();
                    $("#message")
                            .html(`<span style="color:green;">${Translation.ChangesWasSaved}</span>`);
                }
            });
});
