/* global Translation */

// scripts for meta keywords settings page
$(function () {
    $("#meta_keywords_settings")
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