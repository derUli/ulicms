/* global Translation */

// Scripts for simple settings page
$(function () {
    $("#settings_simple")
            .ajaxForm(
                    {
                        beforeSubmit: function () {
                            $("#message").html("");
                            $("#loading").show();
                        },
                        success: function () {
                            $("#loading").hide();
                            $("#message")
                                    .html(`<span style="color:green;">${Translation.ChangesWasSaved}</span>`);
                        }
                    });
});
