/* global Translation */

// scripts for meta keywords settings page
$(function () {
    $("#meta_keywords_settings").ajaxForm(
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