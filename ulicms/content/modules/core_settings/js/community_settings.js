/* global Translation */

// scripts for meta description settings page
$(() => {
    $("#community_settings_form").ajaxForm(
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
