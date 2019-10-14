/* global SettingsTranslation */

$(() => {
    $("#spamfilter_settings").ajaxForm({beforeSubmit: () => {
            $("#message").html("");
            $("#loading").show();
        },
        success: () => {
            $("#loading").hide();
            $("#message").html("<span style=\"color:green;\">" + SettingsTranslation.ChangesWasSaved + "</span>");
        }
    });

    $("#spamfilter_enabled").change((event) => {
        if (event.target.checked) {
            $('#country_filter_settings').slideDown();
        } else {
            $('#country_filter_settings').slideUp();
        }
    });
});