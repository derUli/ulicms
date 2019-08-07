/* global Translation */

// Scripts for simple settings page
$(() => {
    $("#settings_simple")
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
