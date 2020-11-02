/* global Translation */

// scripts for meta description settings page
$(() => {
    $("#site_slogan_settings").ajaxForm({beforeSubmit: function (e) {
            $("#message").html("");
            $("#loading").show();
        },
        success: function (e) {
            $("#loading").hide();
            // FIXME: missing translation, extract to js file.
            $("#message")
                    .html(`<span style="color:green;">${Translation.ChangesWasSaved}</span>`);
        }
    });
});