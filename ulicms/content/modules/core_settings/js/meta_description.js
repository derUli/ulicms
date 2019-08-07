/* global Translation */

// scripts for meta description settings page
$(() => {
    $("#meta_description_settings")
            .ajaxForm(
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
